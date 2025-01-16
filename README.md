# Projeto Laravel - Destak Ferramentas

Este é um projeto de teste para a **Destak Ferramentas**. 
Seu principal objetivo é a integração Oauth2 e cadastro de produtos com a API do Mercado Livre.

Abaixo estão as instruções para instalar e rodar o projeto localmente.

## Requisitos

Antes de começar, você precisará ter os seguintes requisitos instalados:

- **PHP** (recomendado: 8.x)
- **Composer**
- **Node.js** (recomendado: versão LTS)
- **NPM**

## Instalação

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/yodaylay22/Teste-Destak-Ferramentas.git
   cd Teste-Destak-Ferramentas
   ```

2. **Instale as dependências do PHP com o Composer**:
   ```bash
   composer install
   ```

3. **Instale as dependências do JavaScript com o NPM**:
   ```bash
   npm install
   ```

4. **Copie o arquivo `.env.example` para `.env`**:
   ```bash
   cp .env.example .env
   ```

5. **Configure as credenciais da sua aplicação Mercado Livre**:
   Para integrar com o Mercado Livre, é necessário obter um **Client ID** e **Client Secret**. Para isso:
   - Acesse o [Mercado Livre Developers](https://developers.mercadolivre.com.br/devcenter) e crie uma aplicação.
   - Após criar a aplicação, insira as credenciais no arquivo `.env`:

   ```
   MERCADOLIBRE_CLIENT_ID=seu-client-id
   MERCADOLIBRE_CLIENT_SECRET=seu-client-secret
   ```

6. **Gere as tabelas e dados de exemplo**:
   Para cadastrar as categorias e rodar as migrações necessárias, execute o seguinte comando:

   ```bash
   php artisan migrate --seed
   ```

   Isso criará as tabelas no banco de dados e inserirá dados de exemplo (se houver).

## Executando o Projeto

1. **Inicie o servidor local**:

   Se você estiver usando o servidor embutido do PHP, execute o comando abaixo:
   ```bash
   php artisan serve
   ```

2. Acesse o projeto no seu navegador em: `http://localhost:8000`

## Licença

Este projeto é licenciado sob a MIT License.

