<?php
include_once __DIR__ . '/cveapi.php';

// Verifica se há uma busca via GET ou POST
$searchTerm = $_GET['search'] ?? $_POST['search'] ?? 'Servicenow';
$data = CveApi::getCves($searchTerm);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CVE Viewer - <?php echo htmlspecialchars($searchTerm); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            background: white;
            padding: 8px;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .search-input {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 50px;
            font-size: 1em;
            outline: none;
            background: transparent;
        }

        .search-button {
            padding: 15px 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .search-button:active {
            transform: translateY(0);
        }

        .header .stats {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .current-search {
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .table-container {
            overflow-x: auto;
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 18px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: #f8f9ff;
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }

        td {
            padding: 18px;
            vertical-align: top;
        }

        td:nth-child(1) {
            width: 150px;
            font-weight: 600;
        }

        td:nth-child(2) {
            width: 180px;
            color: #666;
        }

        td:nth-child(3) {
            line-height: 1.6;
            color: #333;
        }

        .cve-link {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 700;
            padding: 8px 16px;
            background: #f0f3ff;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .cve-link:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .date-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #e8eaf6;
            color: #5e35b1;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .description {
            font-size: 0.95em;
            line-height: 1.7;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            font-size: 1.2em;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #667eea;
            font-size: 1.1em;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }

            .search-form {
                flex-direction: column;
                border-radius: 15px;
            }

            .search-input, .search-button {
                border-radius: 10px;
            }

            th, td {
                padding: 12px;
                font-size: 0.9em;
            }

            .table-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛡️ CVE Vulnerability Viewer</h1>
            
            <div class="search-container">
                <form method="GET" action="" class="search-form">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Digite o termo de busca (ex: Microsoft Exchange, Apache, WordPress...)" 
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        required
                        minlength="3"
                    >
                    <button type="submit" class="search-button">🔍 Buscar</button>
                </form>
            </div>

            <div class="stats">
                <?php 
                $recentCount = min(20, count($data['vulnerabilities'] ?? []));
                $total = $data['totalResults'] ?? count($data['vulnerabilities'] ?? []);
                echo "Exibindo <strong>$recentCount</strong> vulnerabilidades mais recentes de <strong>$total</strong> total";
                ?>
                <div class="current-search">
                    Termo pesquisado: <strong><?php echo htmlspecialchars($searchTerm); ?></strong>
                </div>
            </div>
        </div>

        <div class="table-container">
            <?php if (isset($data['error'])): ?>
                <div class="no-results">
                    ⚠️ Erro: <?php echo htmlspecialchars($data['error']); ?>
                </div>
            <?php elseif (empty($data['vulnerabilities'])): ?>
                <div class="no-results">
                    ℹ️ Nenhuma vulnerabilidade encontrada para "<?php echo htmlspecialchars($searchTerm); ?>".
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>CVE ID</th>
                            <th>Data de Publicação</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recentVulnerabilities = array_slice($data['vulnerabilities'], 0, 20);
                        foreach ($recentVulnerabilities as $vulnerability): 
                        ?>
                            <?php 
                            $cve = $vulnerability['cve'];
                            $cveId = htmlspecialchars($cve['id']);
                            $published = date('d/m/Y H:i', strtotime($cve['published']));
                            $description = htmlspecialchars($cve['descriptions'][0]['value']);
                            ?>
                            <tr>
                                <td>
                                    <a href="https://cve.mitre.org/cgi-bin/cvename.cgi?name=<?php echo $cveId; ?>" 
                                       class="cve-link" 
                                       target="_blank" 
                                       rel="noopener noreferrer">
                                        <?php echo $cveId; ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="date-badge"><?php echo $published; ?></span>
                                </td>
                                <td>
                                    <div class="description"><?php echo $description; ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>