# 📘 Documentação do Projeto PHP Útil

## 🧾 Visão Geral

**Descrição**:
PHP Útil é um projeto desenvolvido com o objetivo de centralizar e padronizar uma coleção de classes e componentes reutilizáveis utilizados nos sistemas internos da Golfran.
Focado principalmente em operações de CRUD, o projeto foi estruturado para integrar-se de forma nativa com o Framework Adianti, promovendo maior produtividade, consistência no código e facilidade de manutenção entre os diversos sistemas PHP da empresa.

Ele atua como uma base comum de utilidades, contendo funcionalidades como:

- Componentes visuais prontos para formulários e grids;
- Helpers para tratamento de dados e mensagens;
- Classes base para formulários e listagens (herança padrão);
- Integração simplificada com permissões e logs;
- Facilidade na criação de telas padronizadas.

O uso do PHP Útil nos projetos da Golfran garante que todos os sistemas compartilhem boas práticas e mantenham um padrão visual e técnico unificado.

**Tecnologias principais**:
- PHP >= 7.x / 8.x
- Adianti Framework
- Banco de Dados (MySQL, PostgreSQL...)
- Servidor Web (Apache)

**Autores / Equipe**:
- Gustavo Zwirtes – Desenvolvedor
- Gustavo Modena – Desenvolvedor
- Gabriel Mânica – Desenvolvedor
- Geison Carlos Shida – Analista
- ...

---

## 🗂️ Estrutura do Projeto

Estrutura típica de projetos com Adianti:

```plaintext
gx4/                                   # Espelho da estrutura de um projeto Adianti
├── control/                            # Controllers das páginas (TPage, TWindow)
├── database/                           # Regras para carregamento de dados
├── include/                            # Templates HTML
├── widget/                             # Bibliotecas auxiliares
├── docs/                               # Documentação do Projeto
│   ├── exemplos/                       # Exemplos de código, como o `default_values`
│   │   ├── exemplo-default-values.md   # Exemplo de uso do `default_values()`
│   │   └── exemplo-outro.md            # Outro exemplo de código
│   ├── configuracao/                   # Arquivos de configuração e instruções
│   │   ├── configuracao-banco.md       # Como configurar o banco de dados
│   │   └── configuracao-firewall.md    # Arquivo de configuração para firewall (exemplo)
│   ├── boas-praticas/                  # Boas práticas de desenvolvimento
│   │   ├── boas-praticas-crud.md       # Como utilizar o CRUD no Adianti de forma eficiente
│   │   └── boas-praticas-permissoes.md # Uso correto de permissões no Adianti
│   ├── tutoriais/                      # Tutoriais completos para novos usuários
│   │   ├── tutorial-installacao.md     # Como instalar o PHP Útil e Adianti
│   │   └── tutorial-configuracao.md    # Como configurar o PHP Útil no seu projeto
│   └── referencias/                    # Referências gerais sobre o framework ou arquitetura
│       ├── referencia-adiante-framework.md # Detalhes sobre o Adianti Framework
│       └── referencia-banco.md         # Arquitetura de banco de dados no projeto
├── index.php                           # Ponto de entrada

```

---

## 📚 Conteúdo da Documentação

- [Exemplo: Usando `default_values()` em TRecord](docs/exemplos/exemplo-default-values.md)
- [Configuração do Banco de Dados](docs/configuracoes/configuracao-banco.md)
- [Boas Práticas de CRUD](docs/boas-praticas/boas-praticas-crud.md)
- [Tutorial de Instalação](docs/tutoriais/tutorial-installacao.md)
- [Referências sobre Adianti Framework](docs/referencias/referencia-adiante-framework.md)

---

## 📦 Instalação

Para instalar o **PHP Útil** no seu projeto, você pode usar o **Composer**. Siga os passos abaixo:

### Passo 1: Adicionar o pacote via Composer

No diretório raiz do seu projeto, execute o seguinte comando para adicionar o pacote ao seu projeto:

```bash
composer require bymgeison/php_util
