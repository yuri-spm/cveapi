🛡️ CVE Vulnerability Viewer

Este é um visualizador de vulnerabilidades simples, desenvolvido em PHP, que utiliza a API 2.0 do National Vulnerability Database (NVD) para buscar e exibir informações sobre Common Vulnerabilities and Exposures (CVEs).

O projeto consiste em uma interface web onde o usuário pode pesquisar por um termo (como um produto, fornecedor ou tecnologia) e visualizar uma lista das vulnerabilidades mais recentes relacionadas, ordenadas por data de publicação.﻿

<img width="800" height="462" alt="image" src="https://github.com/user-attachments/assets/305b3c25-e4ea-47dd-a723-d4c311d479e1" />


﻿✨ Funcionalidades

    Busca por Palavra-chave: Pesquise em toda a base de dados do NVD por qualquer termo.
    Interface Moderna: UI limpa e responsiva para visualização dos dados.
    Paginação Automática: O script busca todos os resultados da API, independentemente da quantidade, e os consolida.
    Resultados Ordenados: As vulnerabilidades são exibidas da mais recente para a mais antiga.
    Links Diretos: Cada CVE na lista possui um link direto para a página oficial do MITRE para análise aprofundada.

⚙️ Pré-requisitos

Para executar este projeto, você precisará de um ambiente de servidor web com:

    PHP (versão 7.4 ou superior recomendada)
    Extensão cURL do PHP habilitada (para fazer as requisições à API)

🔑 Configuração Obrigatória: Chave da API NVD

Para que o script funcione, é essencial obter uma chave de API gratuita do NVD. A API possui limites de requisição mais altos para usuários autenticados.

    Solicite sua Chave de API: Acesse o site do NVD e preencha o formulário para solicitar sua chave: https://nvd.nist.gov/developers/request-an-api-key
    Insira a Chave no Código: Após receber a chave por e-mail, abra o arquivo cveapi.php e substitua o valor da variável $apiKey pela sua chave.

    // Em cveapi.php, linha 7
    private static string $apiKey = "SUA_CHAVE_API_AQUI"; // Substitua pela sua chave

    Atenção: Sem uma chave de API válida, o script retornará um erro de autenticação.

🚀 Instalação e Uso

    Estrutura de Arquivos:
        Coloque ambos os arquivos (index.php e cveapi.php) no mesmo diretório em seu servidor web.
    Acesso: Abra o seu navegador e acesse o arquivo index.php. Por exemplo: http://localhost/cve-viewer/index.php.
    Busca:
        A página carregará com uma busca padrão pelo termo "Office 365".
        Para fazer uma nova busca, digite o termo desejado no campo de pesquisa (ex: Microsoft Exchange, Apache, WordPress) e clique em "Buscar".

📂 Estrutura do Código

    index.php: Contém a interface do usuário (HTML/CSS) e a lógica de apresentação. Ele recebe a entrada do usuário, chama a classe CveApi para obter os dados e os exibe em uma tabela formatada.
    cveapi.php: Contém a classe CveApi, que é responsável por toda a comunicação com a API do NVD. Suas principais funções são:
        getCves(): Realiza a busca completa por palavra-chave, lidando com a paginação da API.
        curlRequest(): Uma função interna que executa as chamadas cURL para a API.

📝 Notas Adicionais

    O script exibe as 20 vulnerabilidades mais recentes na interface, embora busque todas as correspondentes ao termo pesquisado para garantir que as mais novas sejam sempre mostradas.
    O código inclui tratamento básico de erros para falhas de conexão ou respostas inesperadas da API.


Link do repositório: https://github.com/yuri-spm/cveapi



