# CORES PARA FORMATACAO
VERDE='\033[0;32m'
VERMELHO='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${VERDE}>>> INICIANDO CONFIGURAÇÃO AUTOMÁTICA DO PROKI...${NC}"

# variaveis de configuracao do banco
DB_NAME="proki"
DB_USER="proki"
DB_PASS="senha123"
PROJECT_DIR="/var/www/html/proki"

# Verifica se está rodando como root (sudo)
if [ "$EUID" -ne 0 ]; then 
  echo -e "${VERMELHO}Por favor, rode como root (sudo ./tools/deploy.sh)${NC}"
  exit
fi

# CONFIGURANDO O MYSQL
echo -e "${VERDE}>>> Configurando Banco de Dados MySQL...${NC}"

# Cria o banco e o usuário (aceitando conexões locais e remotas )
mysql -u root -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
mysql -u root -e "CREATE USER IF NOT EXISTS '$DB_USER'@'%' IDENTIFIED BY '$DB_PASS';"
mysql -u root -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -u root -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'%';"
mysql -u root -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"

echo -e "${VERDE}>>> Liberando MySQL para a rede...${NC}"
sed -i 's/bind-address.*=.*/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf 2>/dev/null || \
sed -i 's/bind-address.*=.*/bind-address = 0.0.0.0/' /etc/mysql/my.cnf

service mysql restart

#apache
echo -e "${VERDE}>>> Configurando Apache...${NC}"

# Ativa o módulo Rewrite
a2enmod rewrite

# Cria uma configuração específica para o Proki (Mais seguro que editar o apache2.conf)
# Isso permite o .htaccess funcionar
CONFIG_FILE="/etc/apache2/sites-available/proki.conf"

cat > $CONFIG_FILE <<EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot $PROJECT_DIR

    <Directory $PROJECT_DIR>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Ativa o site e desativa o padrão
a2ensite proki.conf
# a2dissite 000-default.conf # Descomente se quiser que o proki seja o único site

service apache2 restart

# permissoes linux
echo -e "${VERDE}>>> Ajustando Permissões de Pasta...${NC}"

# Pega o usuário real que chamou o sudo (para não deixar tudo como root)
REAL_USER=${SUDO_USER:-$USER}

chown -R $REAL_USER:www-data $PROJECT_DIR
chmod -R 775 $PROJECT_DIR

echo -e "${VERDE}>>> SUCESSO! O AMBIENTE ESTÁ PRONTO.${NC}"
echo -e "Acesse via navegador: http://$(hostname -I | awk '{print $1}')/proki"