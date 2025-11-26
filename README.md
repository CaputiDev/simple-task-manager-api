# Proki-Mini / versão Apache e Mysql

---

## Tecnologias Utilizadas

* **Linguagem:** PHP 8+ (Puro, sem frameworks)
* **Banco de Dados:** Mysql
* **Autenticação:** JWT (JSON Web Token)

## Funcionalidades

* **CRUD de Relatórios:** Criar, listar, atualizar e excluir relatórios de serviço.
* **Autenticação JWT:** Proteção de rotas via Token Bearer.
* **Isolamento de Dados:** Usuários comuns veem apenas os seus próprios relatórios.
* **Segurança:** Senhas com hash (bcrypt) e proteção contra injeção SQL (PDO).

## Arquitetura e Padrões do Projeto

### Arquitetura em Camadas (Controller-Service-Repository)

O sistema estende o padrão **MVC** clássico para uma arquitetura mais robusta, garantindo a **Separação de Responsabilidades** (*Separation of Concerns*):

* **Controller:** Responsável apenas por lidar com a camada HTTP (receber a requisição, capturar dados e devolver a resposta JSON). Não contém regras de negócio.
* **Service:** Contém todas as **regras de negócio**, validações e lógica. É agnóstico ao protocolo HTTP ou ao tipo de base de dados.
* **Repository:** Camada exclusiva de acesso aos dados (SQL). Isola as queries e a comunicação com o banco de dados do restante do sistema.
* **Model:** Representação pura das entidades (DTOs) que trafegam entre as camadas.

### Padrão PSR-4

A estrutura de diretórios e nomes de ficheiros segue rigorosamente a recomendação **PSR-4** (PHP Standards Recommendation) para facilitar o **Autoloading** nativo.

* **PascalCase:** Todas as classes e nomes de ficheiros utilizam a notação *PascalCase* (ex: `TaskController.php`, `UserService.php`), onde a primeira letra de cada palavra é maiúscula.

* **Namespaces:** Os namespaces refletem exatamente a estrutura física das pastas (ex: `namespace Controller;` refere-se à pasta `src/Controller/`).

## Estrutura do Projeto

```text
proki-mini/
├── assets/
│   └── diagrama_bd.png # Diagrama ER do Banco de Dados
├── src/
│   ├── Controller/     # Controladores (Entrada da API)
│   ├── Database/       # Conexão e Setup do SQLite
│   ├── Error/          # Exceções personalizadas
│   ├── Http/           # Classes Request e Response
│   ├── Model/          # Definição dos Objetos (Entidades)
│   ├── Repository/     # Acesso ao Banco de Dados (SQL)
│   ├── Service/        # Regras de Negócio e Validações
│   ├── Utils/          # Utilitários (JWT)
│   └── config.php      # Configurações e Autoloader
├── tools/              # Arquivos de exportação (Insomnia/HAR)
├── .gitignore          # Arquivos ignorados pelo Git
├── .htaccess           # Configuração de rotas (Apache)
├── index.php           # Front Controller (Roteador)
├── *.http              # Arquivos de teste (login, tasks, users)
└── README.md           # Documentação
```

## Como executar o projeto

### Rodando no Linux (Apache/MySql)

Se deseja rodar o projeto em um servidor Linux (Ubuntu/Debian) com Apache e Mysql, siga os passos adicionais de permissão:

### Copiar para o diretório Web

```Bash
sudo git clone [https://github.com/CaputiDev/proki-mini.git](https://github.com/CaputiDev/proki-mini.git) /var/www/html/proki
```

### Coloque o projeto na pasta do Apache

em /var/www/html/

### Ajustar Permissões

O Apache precisa de permissão para ler os arquivos e gravar no banco Mysql

```Bash
## Define o usuário atual e o grupo do Apache (www-data) como admins
sudo chown -R $USER:www-data /var/www/html/proki
```

### Script de permissões Linux

```Bash
sudo chmod +x tools/deploy.sh
sudo ./tools/deploy.sh
sudo systemctl restart apache2
```

>Nota: Se estiver usando a versão com MySQL em rede, certifique-se de configurar o arquivo src/Database/Database.php com o IP correto do servidor.

## Configurar o Banco de Dados

Na raiz do projeto, execute o script de setup para criar as tabelas e popular com dados de teste:

```bash
php src/Database/setup.php
```

## Modelo do Banco de dados

![Diagrama ER do Proki](./assets/diagrama_db.png)

### Usuários de Teste (seed)

O script de setup cria automaticamente os seguintes usuários:

| ID| Nome   | Email               |  Senha  | Cargo |
|---|--------|---------------------|---------|-------|
| 1 | Admin  | `admin@admin.com`   | admin   | Admin |
| 2 | Thiago | `thiago@proki.com`  | senha123| User  |
| 3 | Miguel | `miguel@proki.com`  | senha123| User  |
| 4 | Raul   | `raul@proki.com`    | senha123| User  |

---

## Rotas da API

A API roda sob o prefixo `/proki`.

>💡Dica: Você pode usar os arquivos http na raiz do projeto, com a extensão [Rest Client](https://marketplace.visualstudio.com/items?itemName=humao.rest-client) do VScode ou, se preferir, utilize o arquivo [proki_insomnia.json](./tools/proki_insomnia.json) no insomnia ou o [proki.har](./tools/proki.har) em qualquer outro programa para fazer as requisições.

### 🔐 Autenticação

| Método | Endpoint        | Descrição                                 |
|--------|-----------------|-------------------------------------------|
| POST   | /proki/usuarios | Cria uma nova conta de usuário            |
| POST   | /proki/login    | Realiza login e retorna o Token JWT       |

### 📄 Relatórios

| Método | Endpoint                      | Descrição                                         | Auth |
|--------|-------------------------------|---------------------------------------------------|------|
| GET    | /proki/relatorios             | Lista relatórios (seus ou todos se for Admin)     | ✅   |
| GET    | /proki/relatorios/{id}        | Lista relatorio específico                        | ✅   |
| POST   | /proki/relatorios             | Cria um novo relatório                            | ✅   |
| PUT    | /proki/relatorios/{id}        | Atualiza um relatório                             | ✅   |
| DELETE | /proki/relatorios/{id}        | Exclui um relatório                               | ✅   |

---

### 👨‍💼 Usuários (Admin)

| Método | Endpoint                      | Descrição                                              | Auth  |
|--------|-------------------------------|--------------------------------------------------------|------ |
| GET    | /proki/usuarios               | Lista todos os usuários cadastrados (ADMIN)            |  ✅   |
| GET    | /proki/usuarios/{id}          | Ver perfil (o próprio ou Admin visualiza qualquer um)  |  ✅   |

## Colaboradores

<div align="center">

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/caputidev">
        <img src="https://github.com/CaputiDev.png" width="100px;" alt="Foto Thiago"/><br>
        <sub><b>Thiago Caputi</b></sub>
      </a>
    </td>
    <td align="center">
      <a href="https://github.com/raullize">
        <img src="https://github.com/raullize.png" width="100px;" alt="Foto Raul"/><br>
        <sub><b>Raul Lize Teixeira</b></sub>
      </a>
    </td>
    <td align="center">
      <a href="https://github.com/MiguelLewandowski">
        <img src="https://github.com/MiguelLewandowski.png" width="100px;" alt="Foto Miguel"/><br>
        <sub><b>Miguel Leonardo Lewandowski</b></sub>
      </a>
    </td>
  </tr>
</table>

</div>