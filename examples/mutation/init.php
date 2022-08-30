<?php

use App\GraphQL\Query;
use GraphClass\SchemaBuilder;
use GraphClass\SchemaOptions;
use GraphClass\SchemaRequest;

require ("vendor/autoload.php");

$root = __DIR__;
$schema = SchemaBuilder::build(new SchemaOptions(
    schemaFilePath: "$root/schema.gql",
    root: new Query()
));

//$query = file_get_contents("$root/query");
//$response = $schema(new SchemaRequest(
//    query: $query ?: ""
//));

$mutation = file_get_contents("$root/mutation");
$vars = file_get_contents("$root/variables.json");


$response = $schema(new SchemaRequest(
    query: $mutation ?: "",
    variables: json_decode($vars ?: "{}", true, flags: JSON_THROW_ON_ERROR)
));

echo json_encode($response);
echo PHP_EOL;
