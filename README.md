# 🏥 SGHSS API – Sistema de Gestão Hospitalar e de Serviços de Saúde

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)

## 📖 Sobre o Projeto

Este projeto consiste na implementação de uma **API RESTful** para o **Sistema de Gestão Hospitalar e de Serviços de Saúde (SGHSS)** da instituição **VidaPlus**.
O desenvolvimento possui ênfase no Back-end, garantindo segurança, escalabilidade e integridade dos dados.

### Funcionalidades Principais
- **Autenticação e Autorização**: Controle de acesso via ACL (Access Control List).
- **Agendamentos**: Gestão de consultas presenciais e remotas.
- **Prontuários Médicos**: Registro clínico seguro e acessível apenas por profissionais autorizados.
- **Auditoria (LGPD)**: Rastreamento completo de ações sensíveis no sistema.

---

## 🚀 Tecnologias Utilizadas

- **Framework:** [Laravel 11](https://laravel.com)
- **Banco de Dados:** MySQL / MariaDB
- **Autenticação:** Laravel Sanctum (Personal Access Tokens)
- **Ambiente de Desenvolvimento:** Docker (via Laravel Sail)

---

## ⚙️ Instalação e Configuração

Siga os passos abaixo para rodar o projeto em seu ambiente local.

### Pré-requisitos
- [Docker](https://www.docker.com/) e Docker Compose instalados.
- PHP 8.2+ (opcional, caso não use o Sail para comandos iniciais).

### 1. Clone o Repositório

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs

git clone https://github.com/cyn166/sghss.git
cd sghss
```

### 2. Configuração de Ambiente

Copie o arquivo de exemplo e gere a chave da aplicação:

```bash
cp .env.example .env
```

### 3. Configuração do Banco de Dados

Certifique-se de que as credenciais no arquivo `.env` estejam corretas para o ambiente Docker:

```ini
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

### 4. Iniciar o Ambiente

Suba os containers do Docker:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

### 5. Executar Migrações

Crie as tabelas no banco de dados:

```bash
./vendor/bin/sail artisan migrate
```

A API estará disponível em: `http://localhost:8000`

---

## 🔒 Documentação da API

A API segue os padrões REST. Todas as requisições devem incluir o header:
`Accept: application/json`

### 🔐 Autenticação

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `POST` | `/api/register` | Registra um novo usuário e cria seu perfil específico. |
| `POST` | `/api/login` | Autentica o usuário e retorna um token Sanctum. |
| `DELETE` | `/api/logout` | Revoga o token de acesso atual. |

#### Exemplo: Registro de Paciente (`POST /api/register`)

**Body:**
```json
{
  "name": "Joana Paciente Teste",
  "email": "joana.teste@sghss.com",
  "password": "password",
  "role": "paciente",
  "cpf": "111.222.333-44",
  "birth_date": "01-01-2000",
  "phone": "21988887777",
  "address": "Rua Principal, 10",
  "blood_type": "O+",
  "emergency_contact": "1199999999"
}
```

#### Exemplo: Registro de Médico (`POST /api/register`)

**Body:**
```json
{
  "name": "Marcos Médico Teste",
  "email": "marcos.teste@sghss.com",
  "password": "password",
  "role": "medico",
  "cpf": "111.222.333-44",
  "crm": "123762193",
  "phone": "21988887777",
  "specialty": "1399999999",
  "available_hours": "{seg: 08-12, ter: 14-18}"
}
```

#### Exemplo: Registro de Enfermeiro (`POST /api/register`)

**Body:**
```json
{
  "name": "Sofia Enfermeira Teste",
  "email": "sofia.teste@sghss.com",
  "password": "password",
  "role": "enfermeiro",
  "cpf": "111.222.333-44",
  "license_number": "93456439",
  "license_expiry_date": "09-10-2030"
  "phone": "21988887777",
  "specialty": "Geral",
  "available_hours": "{seg: 08-12, ter: 14-18}"
}
```

#### Exemplo: Login (`POST /api/login`)

**Body:**
```json
{
  "email": "joana.teste@sghss.com",
  "password": "password"
}
```

---

### 📅 Agendamentos (Appointments)

| Método | Endpoint | Descrição | Acesso |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/appointments` | Cria um novo agendamento | Paciente |
| `GET` | `/api/appointments` | Lista agendamentos | Todos (com filtros) |
| `GET` | `/api/appointments/{id}` | Detalhes do agendamento | Policy |
| `PUT` | `/api/appointments/{id}` | Atualiza ou cancela | Paciente |
| `DELETE` | `/api/appointments/{id}` | Remove um agendamento | Paciente |

#### Exemplo: Criar Agendamento (`POST /api/appointments`)

**Body:**
```json
{
  "doctor_id": 1,
  "nurse_id": null,
  "appointment_date": "2026-02-15 14:30:00",
  "type": "presencial",
  "notes": "Consulta anual de rotina."
}
```

---

### ⚕️ Prontuários Médicos (Medical Records)

| Método | Endpoint | Acesso |
| :--- | :--- | :--- |
| `POST` | `/api/medical-records` | Médico/Enfermeiro responsável |
| `GET` | `/api/medical-records/{id}` | Criador ou Admin |
| `PUT` | `/api/medical-records/{id}` | Criador ou Admin |

#### Exemplo: Criar Prontuário (`POST /api/medical-records`)

**Body:**
```json
{
  "appointment_id": 5,
  "diagnosis": "Gripe Comum",
  "treatment": "Repouso e hidratação",
  "prescription": "Dipirona 500mg a cada 6 horas",
  "notes": "Paciente apresenta quadro estável."
}
```

---

### 👤 Pacientes 

| Método | Endpoint | Acesso |
| :--- | :--- | :--- |
| `GET` | `/patients/{id}` | Médico/Enfermeiro responsável |

#### Exemplo: Consultar paciente (`GET /patients/1`)

**Body:**
```json
{
    "id": 1,
    "user": {
        "id": 1,
        "name": "Guilherme",
        "email": "guilherme@vidaplus.com",
        "role": "paciente"
    },
    "patient_details": {
        "address": "Rua do Paciente",
        "phone": "2499999999",
        "date_of_birth": null,
        "blood_type": "A+",
        "emergency_contact": "1199999999"
    },
    "total_appointments": 1,
    "appointments": [
        {
            "id": 3,
            "appointment_date": "2026-01-20T10:00:00.000000Z",
            "doctor": {
                "id": 1,
                "name": "João da Silva",
                "email": "joao@vidaplus.com"
            },
            "nurse": null,
            "status": "concluido"
        }
    ]
}
```

---

### 📊 Auditoria (Logs)

O sistema registra ações críticas para conformidade com a LGPD.

| Método | Endpoint | Descrição | Acesso |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/audit-logs` | Lista todos os logs de auditoria | Admin |

#### Exemplo de Resposta

```json
{
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "action": "UPDATE",
      "table_affected": "appointments",
      "description": {
        "record_id": 5,
        "new_data": { "status": "concluido" },
        "original_data": { "status": "scheduled" }
      },
      "ip_address": "172.21.0.1",
      "created_at": "2025-12-10T14:30:00.000000Z"
    }
  ]
}
```

---

## 📄 Licença

Este projeto é de propriedade intelectual do criador. O código fonte está disponível publicamente apenas para fins de avaliação acadêmica na disciplina de Projeto Multidisciplinar da UNINTER. É vedada a sua utilização comercial ou modificação por terceiros.

---

## 👤 Autor

Guilherme Guedes - RU: 4553437
