<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use JsonSchema\Constraints\Constraint;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

$version = '3_00';

$jsonSchema = '{
    "title": "RPS",
    "type": "object",
    "properties": {
        "version": {
            "required": true,
            "type": "string"
        },
        "identificacaorps": {
            "required": true,
            "type": "object",
            "properties": {
                "numero": {
                    "required": true,
                    "type": "integer",
                    "pattern": "^[0-9]{1,15}$"
                },
                "serie": {
                    "required": true,
                    "type": "string",
                    "maxLength": 5,
                    "pattern": "^[0-9A-Za-z]{1,5}$"
                },
                "tipo": {
                    "required": true,
                    "type": "integer",
                    "pattern": "^[1-3]{1}"
                }
            }
        },
        "dataemissao": {
            "required": true,
            "type": "string",
            "pattern": "^([0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T(2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9])$"
        },
        "naturezaoperacao": {
            "required": true,
            "type": "integer",
            "pattern": "^[1-6]{1}$"
        },
        "regimeespecialtributacao": {
            "required": true,
            "type": "integer",
            "pattern": "^[1-6]{1}$"
        },
        "optantesimplesnacional": {
            "required": true,
            "type": "integer",
            "pattern": "^[1-2]{1}$"
        },
        "incentivadorcultural": {
            "required": true,
            "type": "integer",
            "pattern": "^[1-2]{1}$"
        },
        "status": {
            "required": true,
            "type": "integer",
            "pattern": "^[1-2]{1}$"
        },
        "tomador": {
            "required": true,
            "type": "object",
            "properties": {
                "cnpj": {
                    "required": false,
                    "type": ["string","null"],
                    "pattern": "^[0-9]{14}$"
                },
                "cpf": {
                    "required": false,
                    "type": ["string","null"],
                    "pattern": "^[0-9]{11}$"
                },
                "inscricaomunicipal": {
                    "required": false,
                    "type": ["string","null"],
                    "minLength": 1,
                    "maxLength": 15
                },
                "razaosocial": {
                    "required": true,
                    "type": "string",
                    "minLength": 1,
                    "maxLength": 115
                },
                "endereco": {
                    "required": false,
                    "type": ["object","null"],
                    "properties": {
                        "endereco": {
                            "required": true,
                            "type": "string",
                            "minLength": 1,
                            "maxLength": 125
                        },
                        "numero": {
                            "required": true,
                            "type": "string",
                            "minLength": 1,
                            "maxLength": 10
                        },
                        "complemento": {
                            "required": false,
                            "type": ["string","null"],
                            "minLength": 1,
                            "maxLength": 60
                        },
                        "bairro": {
                            "required": true,
                            "type": "string",
                            "minLength": 1,
                            "maxLength": 60
                        },
                        "codigomunicipio": {
                            "required": true,
                            "type": "integer",
                            "pattern": "^[0-9]{7}$"
                        },
                        "uf": {
                            "required": true,
                            "type": "string",
                            "maxLength": 2
                        },
                        "cep": {
                            "required": true,
                            "type": "integer",
                            "pattern": "^[0-9]{8}$"
                        }
                    }
                }
            }
        },
        "servico": {
            "required": true,
            "type": "object",
            "properties": {
                "itemlistaservico": {
                    "required": true,
                    "type": "string",
                    "minLength": 1,
                    "maxLength": 5
                },
                "codigotributacaomunicipio": {
                    "required": true,
                    "type": "string",
                    "minLength": 1,
                    "maxLength": 20
                },
                "discriminacao": {
                    "required": true,
                    "type": "string",
                    "minLength": 1,
                    "maxLength": 2000
                },
                "codigomunicipio": {
                    "required": true,
                    "type": "integer",
                    "pattern": "^[0-9]{7}$"
                },
                "valores": {
                    "required": true,
                    "type": "object",
                    "properties": {
                        "valorservicos": {
                            "required": true,
                            "type": "number"
                        },
                        "valordeducoes": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "valorpis": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "valorcofins": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "valorinss": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "valorir": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "valorcsll": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "issretido": {
                            "required": true,
                            "type": "integer",
                            "pattern": "^[1-2]{1}$"
                        },
                        "valoriss": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "valorissretido": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "outrasretencoes": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "basecalculo": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "aliquota": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "valorliquidonfse": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "descontoincondicionado": {
                            "required": false,
                            "type": ["number", "null"]
                        },
                        "descontocondicionado": {
                            "required": false,
                            "type": ["number", "null"]
                        }
                    }
                }
            }
        },
        "intermediarioservico": {
            "required": false,
            "type": ["object","null"],
            "properties": {
                "razaosocial": {
                    "required": true,
                    "type": "string",
                    "minLength": 1,
                    "maxLength": 115
                },
                "cnpj": {
                    "required": false,
                    "type": ["string","null"],
                    "pattern": "^[0-9]{14}$"
                },
                "cpf": {
                    "required": false,
                    "type": ["string","null"],
                    "pattern": "^[0-9]{11}$"
                },
                "inscricaomunicipal": {
                    "required": false,
                    "type": ["string","null"],
                    "minLength": 1,
                    "maxLength": 15
                }
            }
        },
        "construcaocivil": {
            "required": false,
            "type": ["object","null"],
            "properties": {
                "codigoobra": {
                    "required": true,
                    "type": "string",
                    "minLength": 1,
                    "maxLength": 15
                },
                "art": {
                    "required": true,
                    "type": "string",
                    "minLength": 1,
                    "maxLength": 15
                }
            }
        },
        "orgaogerador": {
            "required": false,
            "type": ["object","null"],
            "properties": {
                "codigomunicipio": {
                    "required": true,
                    "type": "integer",
                    "pattern": "^[0-9]{7}$"
                },
                "uf": {
                    "required": true,
                    "type": "string",
                    "minLength": 2,
                    "maxLength": 2
                }
            }
        }
    }
}';


$std = new \stdClass();
$std->version = '1.00';
$std->identificacaorps = new \stdClass();
$std->identificacaorps->numero = 11; //limite 15 digitos
$std->identificacaorps->serie = '1'; //BH deve ser string numerico
$std->identificacaorps->tipo = 1; //1 - RPS 2-Nota Fiscal Conjugada (Mista) 3-Cupom
$std->dataemissao = '2018-10-31T12:33:22';
$std->naturezaoperacao = 1; // 1 – Tributação no município
                            // 2 - Tributação fora do município
                            // 3 - Isenção
                            // 4 - Imune
                            // 5 – Exigibilidade suspensa por decisão judicial
                            // 6 – Exigibilidade suspensa por procedimento administrativo

$std->regimeespecialtributacao = 1;    // 1 – Microempresa municipal
                                       // 2 - Estimativa
                                       // 3 – Sociedade de profissionais
                                       // 4 – Cooperativa
                                       // 5 – MEI – Simples Nacional
                                       // 6 – ME EPP – Simples Nacional

$std->optantesimplesnacional = 1; //1 - SIM 2 - Não
$std->incentivadorcultural = 2; //1 - SIM 2 - Não
$std->status = 1;  // 1 – Normal  2 – Cancelado

$std->tomador = new \stdClass();
$std->tomador->cnpj = "99999999000191";
$std->tomador->cpf = "12345678901";
$std->tomador->razaosocial = "Fulano de Tal";

$std->tomador->endereco = new \stdClass();
$std->tomador->endereco->endereco = 'Rua das Rosas';
$std->tomador->endereco->numero = '111';
$std->tomador->endereco->complemento = 'Sobre Loja';
$std->tomador->endereco->bairro = 'Centro';
$std->tomador->endereco->codigomunicipio = '3106200';
$std->tomador->endereco->uf = 'MG';
$std->tomador->endereco->cep = '30160010';

$std->servico = new \stdClass();
$std->servico->itemlistaservico = '11.01';
$std->servico->codigotributacaomunicipio = '522310000';
$std->servico->discriminacao = 'Teste de RPS';
$std->servico->codigomunicipio = '3106200';

$std->servico->valores = new \stdClass();
$std->servico->valores->valorservicos = 100.00;
$std->servico->valores->valordeducoes = 10.00;
$std->servico->valores->valorpis = 10.00;
$std->servico->valores->valorcofins = 10.00;
$std->servico->valores->valorinss = 10.00;
$std->servico->valores->valorir = 10.00;
$std->servico->valores->valorcsll = 10.00;
$std->servico->valores->issretido = 1;
$std->servico->valores->valoriss = 10.00;
$std->servico->valores->valorissretido = 10.00;
$std->servico->valores->outrasretencoes = 10.00;
$std->servico->valores->basecalculo = 10.00;
$std->servico->valores->aliquota = 5;
$std->servico->valores->valorliquidonfse = 10.00;
$std->servico->valores->descontoincondicionado = 10.00;
$std->servico->valores->descontocondicionado = 10.00;

$std->Intermediarioservico = new \stdClass();
$std->Intermediarioservico->RazaoSocial = 'INSCRICAO DE TESTE SIATU - D AGUA -PAULINO S'; 
$std->Intermediarioservico->Cnpj = '99999999000191';
$std->Intermediarioservico->InscricaoMunicipal = '8041700010';

$std->construcaocivil = new \stdClass();
$std->construcaocivil->codigoobra = '1234';
$std->construcaocivil->art = '1234';

$std->orgaogerador = new \stdClass();
$std->orgaogerador->codigomunicipio = '3106200';
$std->orgaogerador->uf = 'MG';

// Schema must be decoded before it can be used for validation
$jsonSchemaObject = json_decode($jsonSchema);
if (empty($jsonSchemaObject)) {
    echo "<h2>Erro de digitação no schema ! Revise</h2>";
    echo "<pre>";
    print_r($jsonSchema);
    echo "</pre>";
    die();
}
// The SchemaStorage can resolve references, loading additional schemas from file as needed, etc.
$schemaStorage = new SchemaStorage();
// This does two things:
// 1) Mutates $jsonSchemaObject to normalize the references (to file://mySchema#/definitions/integerData, etc)
// 2) Tells $schemaStorage that references to file://mySchema... should be resolved by looking in $jsonSchemaObject
$schemaStorage->addSchema('file://mySchema', $jsonSchemaObject);
// Provide $schemaStorage to the Validator so that references can be resolved during validation
$jsonValidator = new Validator(new Factory($schemaStorage));
// Do validation (use isValid() and getErrors() to check the result)
$jsonValidator->validate(
    $std,
    $jsonSchemaObject,
    Constraint::CHECK_MODE_COERCE_TYPES  //tenta converter o dado no tipo indicado no schema
);

if ($jsonValidator->isValid()) {
    echo "The supplied JSON validates against the schema.<br/>";
} else {
    echo "Dados não validados. Violações:<br/>";
    foreach ($jsonValidator->getErrors() as $error) {
        echo sprintf("[%s] %s<br/>", $error['property'], $error['message']);
    }
    die;
}
//salva se sucesso
file_put_contents("../storage/jsonSchemes/v$version/rps.schema", $jsonSchema);