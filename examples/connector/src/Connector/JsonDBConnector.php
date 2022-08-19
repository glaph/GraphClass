<?php

namespace App\Connector;

use GraphClass\Type\Connector\Connector;
use GraphClass\Type\Connector\Request;
use GraphClass\Type\Connector\Response;
use GraphClass\Type\Connector\Response\Keys;

class JsonDBConnector implements Connector {
    public function retrieve(Request $request, Response $response): void {
        $root = dirname(__DIR__, 2);
        $jsonPath = "$root/db/$request->group.json";
        $json = file_get_contents($jsonPath);

        if ($json === false) return;

        $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        foreach ($request->keys as $hash => $key) {
            if (!isset($data[$key["id"]])) continue;
            $itemValues = [
                "id" => $key["id"]
            ];
            foreach ($request->fields as $fieldName => $value) {
                $itemValues[$fieldName] = $data[$key["id"]][$fieldName];
            }
            $response->addItem(new Response\Item($hash, $itemValues));
        }
    }

    public function submit(Request $request, Response $response): ?Keys{
        return null;
    }
}
