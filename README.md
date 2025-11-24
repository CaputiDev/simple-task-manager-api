# Proki-Mini

---

## Tecnologias Utilizadas

* **Linguagem:** PHP 8+ (Puro, sem frameworks)
**Banco de Dados:** SQLite
* **Autenticação:** JWT (JSON Web Token)

## Funcionalidades

* **CRUD de Relatórios:** Criar, listar, atualizar e excluir relatórios de serviço.
* **Autenticação JWT:** Proteção de rotas via Token Bearer.
* **Isolamento de Dados:** Usuários comuns veem apenas os seus próprios relatórios.
* **Segurança:** Senhas com hash (bcrypt) e proteção contra injeção SQL (PDO).

---

## Estrutura do Projeto

```text
proki/
├── src/
│   ├── Controller/    # Controladores (Entrada da API)
│   ├── Service/       # Regras de Negócio e Validações
│   ├── Repository/    # Acesso ao Banco de Dados (SQL)
│   ├── Model/         # Definição dos Objetos (Entidades)
│   ├── Http/          # Classes Request e Response
│   ├── Utils/         # Utilitários (JWT, Config)
│   └── database/      # Arquivo SQLite e script de Setup
├── tools/
│   ├── proki_insomnia.json # Collection para importação no Insomnia
│   └── proki.har           # Log de requisições HTTP genérico
├── .htaccess          # Configuração de rotas (Apache)
├── index.php          # Front Controller (Roteador)
├── *.http             # Arquivos de teste (REST Client VS Code)
└── README.md          # Documentação
```

## Como rodar o projeto

1. Clonar o repositório

```bash
git clone https://github.com/CaputiDev/proki-mini
```

2. Configurar o Banco de Dados

Na raiz do projeto, execute o script de setup para criar as tabelas e popular com dados de teste:

```bash
php src/database/setup.php
```

---

### Usuários de Teste (seed)

O script de setup cria automaticamente os seguintes usuários:

| ID| Nome   | Email               |  Senha  | Cargo |
|---|--------|---------------------|---------|-------|
| 1 | Admin  | `admin@admin.com`   | admin   | Admin |
| 2 | Thiago | `thiago@proki.com`  | senha123| User  |
| 3 | Miguel | `miguel@proki.com`  | senha123| User  |
| 4 | Raul   | `raul@proki.com`    | senha123| User  |

3. Iniciar o Servidor

Na raiz do projeto, inicie o servidor (pode utilizar o embutido do PHP ou outro servidor web):

```bash
php -S localhost:80
```

---

## Rotas da API

A API roda sob o prefixo `/proki`.

>💡Dica: Você pode usar os arquivos http na raiz do projeto, com a extensão [Rest Client](https://marketplace.visualstudio.com/items?itemName=humao.rest-client) do VScode ou, se preferir, utilize o arquivo [proki_insomnia.json](./tools/proki_insomnia.json) no insomnia ou o [proki.har](./tools/proki.har) em qualquer outro programa para fazer as requisições.

### 🔐 Autenticação

| Método | Endpoint        | Descrição                                 |
|--------|-----------------|-------------------------------------------|
| POST   | /proki/login    | Realiza login e retorna o Token JWT       |
| POST   | /proki/usuarios | Cria uma nova conta de usuário            |

### 📄 Relatórios

| Método | Endpoint                      | Descrição                                         | Auth |
|--------|-------------------------------|---------------------------------------------------|------|
| GET    | /proki/relatorios             | Lista relatórios (seus ou todos se for Admin)     | ✅   |
| GET    | /proki/relatorios/{id}        | Lista relatorio específico                        | ✅   |
| POST   | /proki/relatorios             | Cria um novo relatório                            | ✅   |
| PUT    | /proki/relatorios/{id}        | Atualiza um relatório                             | ✅   |
| DELETE | /proki/relatorios/{id}        | Exclui um relatório                               | ✅   |

---

### 👥 Usuários (Admin)

| Método | Endpoint                      | Descrição                                              | Auth  |
|--------|-------------------------------|--------------------------------------------------------|------ |
| GET    | /proki/usuarios               | Lista todos os usuários cadastrados (ADMIN)            |  ✅   |
| GET    | /proki/usuarios/{id}          | Ver perfil (o próprio ou Admin visualiza qualquer um)  |  ✅   |

## 👥 Colaboradores

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