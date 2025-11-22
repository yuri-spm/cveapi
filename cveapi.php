<?php

class CveApi
{

    private static string $nvdBase   = "https://services.nvd.nist.gov/rest/json/cves/2.0";
    private static string $apiKey    = "Sua chave aqui"; // Substitua pela sua chave de API NVD
    private static string $cfuvidCookie = "_cfuvid=KXqkKk.ql6se0OPv3VUayRtcxUpgmYyPommNdiJESmI-1762996338.5386074-1.0.1.1-g.MVGS_N3SuCaOvMr8wx91B.nfLNy6z099fmoU.5eZ4";


    private static function curlRequest(string $url, array $headers = []): ?array
    {
        error_log("[REQUEST] URL: $url");

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => $headers
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            return ["error" => "Erro cURL: $err"];
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            return ["error" => "Erro HTTP $httpCode ao acessar $url"];
        }

        return json_decode($response, true);
    }




    public static function getCves(string $keyword): array
    {
        $allVulnerabilities = [];
        $startIndex = 0;
        $resultsPerPage = 2000;
        $totalResults = 0;

        do {
            $url = self::$nvdBase . '?' . http_build_query([
                'keywordSearch'  => $keyword,
                'resultsPerPage' => $resultsPerPage,
                'startIndex'     => $startIndex,
            ]);

            $headers = [
                "api_key: " . self::$apiKey,
                "Cookie: " . self::$cfuvidCookie,
                "Accept: application/json"
            ];

            $data = self::curlRequest($url, $headers);

            if (!is_array($data) || isset($data["error"])) {
                return [
                    "error" => $data["error"] ?? "Erro ao conectar à API NVD via cURL."
                ];
            }

            if ($startIndex === 0) {
                if (!isset($data["vulnerabilities"])) {
                    return ["error" => "Nenhum dado retornado pela API NVD. Verifique a API Key."];
                }
                $totalResults = $data["totalResults"] ?? 0;
                if ($totalResults === 0) {
                    return [
                        "totalResults" => 0,
                        "vulnerabilities" => []
                    ];
                }
            }

            if (empty($data["vulnerabilities"])) {
                break;
            }

            $allVulnerabilities = array_merge($allVulnerabilities, $data["vulnerabilities"]);
            $startIndex += $resultsPerPage;
        } while ($startIndex < $totalResults);

        usort($allVulnerabilities, function ($a, $b) {
            $dateA = strtotime($a["cve"]["published"] ?? "1970-01-01");
            $dateB = strtotime($b["cve"]["published"] ?? "1970-01-01");
            return $dateB <=> $dateA;
        });

        return [
            "totalResults"    => count($allVulnerabilities),
            "vulnerabilities" => $allVulnerabilities
        ];
    }


    public static function getCVE(string $id): array
    {
        if (empty($id)) {
            return ["error" => "ID vazio"];
        }

        $url = self::$mitreBase . urlencode($id);

        return self::curlRequest($url);
    }


    public static function searchKeyword(string $keyword): array
    {
        if (strlen($keyword) < 3) {
            return ["error" => "Informe pelo menos 3 caracteres."];
        }

        $url = self::$nvdBase . "?keywordSearch=" . urlencode($keyword) . "&resultsPerPage=200";

        $headers = [
            "api_key: " . self::$apiKey,
            "Cookie: " . self::$cfuvidCookie
        ];

        return self::curlRequest($url, $headers);
    }

    public static function searchMultipleTerms(array $terms): array
    {
        $seen = [];
        $final = [];

        foreach ($terms as $term) {
            $data = self::searchKeyword($term);

            if (isset($data["vulnerabilities"])) {
                foreach ($data["vulnerabilities"] as $v) {
                    $id = $v["cve"]["id"];

                    if (!isset($seen[$id])) {
                        $seen[$id] = true;
                        $final[] = $v;
                    }
                }
            }

            sleep(1);
        }

        return ["vulnerabilities" => $final];
    }


    public static function testConnection(): array
    {
        $url = self::$circlBase . "/last";

        $result = self::curlRequest($url);

        if (isset($result["error"])) {
            return [
                "success" => false,
                "message" => $result["error"],
                "details" => [
                    "url_base"       => self::$circlBase,
                    "curl_available" => function_exists('curl_init'),
                    "ssl_enabled"    => true,
                    "api_key_set"    => true,
                    "api_key_length" => strlen(self::$apiKey),
                    "total_results"  => 0
                ]
            ];
        }

        return [
            "success" => true,
            "message" => "Conexão OK",
            "details" => [
                "url_base"       => self::$circlBase,
                "curl_available" => function_exists('curl_init'),
                "ssl_enabled"    => true,
                "api_key_set"    => true,
                "api_key_length" => strlen(self::$apiKey),
                "total_results"  => count($result)
            ]
        ];
    }


    public static function clearCache(): bool
    {
        return true;
    }
}
