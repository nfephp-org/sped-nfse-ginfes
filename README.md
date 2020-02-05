# sped-nfse-ginfes

Api para comunicação com webservices do Projeto NFSe Ginfes

## BETHA TESTS

*Utilize o chat do Gitter para iniciar discussões especificas sobre o desenvolvimento deste pacote.*


[![Latest Stable Version][ico-stable]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![License][ico-license]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

[![Issues][ico-issues]][link-issues]
[![Forks][ico-forks]][link-forks]
[![Stars][ico-stars]][link-stars]

Este pacote é aderente com os [PSR-1], [PSR-2] e [PSR-4]. Se você observar negligências de conformidade, por favor envie um patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

Não deixe de se cadastrar no [grupo de discussão do NFePHP](http://groups.google.com/group/nfephp) para acompanhar o desenvolvimento e participar das discussões e tirar duvidas!

**NOTA: Fique atento pois nem todas as cidades possuem ambientes de homologação.**

## Municípios atendidos pelo provedor

|n|Município|UF|Ibge|
|:---:|:---|:---:|:---:|
|1|Amparo|SP|3501905|
|2|Ananindeua|SP|1500800|
|3|Araraquara|SP|3503208|
|4|Bertioga|SP|3506359|
|5|Betim|SP|3106705|
|6|Campos dos Goytacazes|SP|3301009|
|7|Capivari|SP|3510401|
|8|Caruaru|SP|2604106|
|9|Cataguases|SP|3115300|
|10|Colina|SP|3512001|
|11|Conceicao do Mato Dentro|SP|3117504|
|12|Contagem|SP|3118601|
|13|Diadema|SP|3513801|
|14|Embu-Guacu|SP|3515103|
|15|Franca|SP|3516200|
|16|Guararema|SP|3518305|
|17|Guaruja|SP|3518701|
|18|Guarulhos|SP|3518800|
|19|Hortolandia|SP|3519071|
|20|Itaborai|SP|3301900|
|21|Itajuba|SP|3132404|
|22|Itauna|SP|3133808|
|23|Itu|SP|3523909|
|24|Jaboticabal|SP|3524303|
|25|Jardinopolis|SP|3525102|
|26|Jundiai|SP|3525904|
|27|Lagoa Santa|SP|3137601|
|28|Maceio|SP|2704302|
|29|Marechal Deodoro|SP|2704708|
|30|Marica|SP|3302700|
|31|Matao|SP|3529302|
|32|Maua|SP|3529401|
|33|Mineiros|SP|5213103|
|34|Mococa|SP|3530508|
|35|Morro Agudo|SP|3531902|
|36|Muriae|SP|3143906|
|37|Olimpia|SP|3533908|
|38|Oliveira|SP|3145604|
|39|Para de Minas|SP|3147105|
|40|Paranagua|SP|4118204|
|41|Paulinia|SP|3536505|
|42|Porto Ferreira|SP|3540705|
|43|Pouso Alegre|SP|3152501|
|44|Registro|SP|3542602|
|45|Ribeirao Pires|SP|3543303|
|46|Ribeirao Preto|SP|3543402|
|47|Rio Bonito|SP|3304300|
|48|Rio Claro|SP|3543907|
|49|Sacramento|SP|3156908|
|50|Salto|SP|3545209|
|51|Santarem|SP|2513653|
|52|Santarém|SP|1506807|
|53|Santo Andre|SP|3547809|
|54|Santos|SP|3548500|
|55|Sao Bernardo do Campos|SP|3548708|
|56|Sao Caetano do Sul|SP|3548807|
|57|Sao Carlos|SP|3548906|
|58|Sao Jose do Rio Preto|SP|3549805|
|59|Sao Roque|SP|3550605|
|60|Sao Sebastiao|SP|3550704|
|61|Suzano|SP|3552502|
|62|Taquaritinga|SP|3553708|
|63|Ubatuba|SP|3555406|
|64|Umuarama|SP|4128104|
|65|Varginha|SP|3170701|
|66|Votuporanga|SP|3557105|


## Dependências

- PHP >= 7.1
- ext-curl
- ext-soap
- ext-zlib
- ext-dom
- ext-openssl
- ext-json
- ext-simplexml
- ext-libxml

### Outras Libs

- nfephp-org/sped-common
- justinrainbow/json-schema

## Contribuindo
Este é um projeto totalmente *OpenSource*, para usa-lo e modifica-lo você não paga absolutamente nada. Porém para continuarmos a mante-lo é necessário qua alguma contribuição seja feita, seja auxiliando na codificação, na documentação ou na realização de testes e identificação de falhas e BUGs.

**Este pacote esta listado no [Packgist](https://packagist.org/) foi desenvolvido para uso do [Composer](https://getcomposer.org/), portanto não será explicitada nenhuma alternativa de instalação.**

*Durante a fase de desenvolvimento e testes este pacote deve ser instalado com:*
```bash
composer require nfephp-org/sped-nfse-ginfes:dev-master
```

*Ou ainda,*
```bash
composer require nfephp-org/sped-nfse-ginfes:dev-master --prefer-dist
```

*Ou ainda alterando o composer.json do seu aplicativo inserindo:*
```json
"require": {
    "nfephp-org/sped-nfse-ginfes" : "dev-master"
}
```

> NOTA: Ao utilizar este pacote ainda na fase de desenvolvimento não se esqueça de alterar o composer.json da sua aplicação para aceitar pacotes em desenvolvimento, alterando a propriedade "minimum-stability" de "stable" para "dev".
> ```json
> "minimum-stability": "dev",
> "prefer-stable": true
> ```

*Após os stable realeases estarem disponíveis, este pacote poderá ser instalado com:*
```bash
composer require nfephp-org/sped-nfse-ginfes
```
Ou ainda alterando o composer.json do seu aplicativo inserindo:
```json
"require": {
    "nfephp-org/sped-sped-nfse-ginfes" : "^1.0"
}
```

## Forma de uso
vide a pasta *Examples*

## Log de mudanças e versões
Acompanhe o [CHANGELOG](CHANGELOG.md) para maiores informações sobre as alterações recentes.

## Testing

Todos os testes são desenvolvidos para operar com o PHPUNIT

## Security

Caso você encontre algum problema relativo a segurança, por favor envie um email diretamente aos mantenedores do pacote ao invés de abrir um ISSUE.

## Credits

Cleiton Perin (owner and developer)

## License

Este pacote está diponibilizado sob LGPLv3 ou MIT License (MIT). Leia  [Arquivo de Licença](LICENSE.md) para maiores informações.


[ico-stable]: https://poser.pugx.org/nfephp-org/sped-nfse-ginfes/version
[ico-stars]: https://img.shields.io/github/stars/nfephp-org/sped-nfse-ginfes.svg?style=flat-square
[ico-forks]: https://img.shields.io/github/forks/nfephp-org/sped-nfse-ginfes.svg?style=flat-square
[ico-issues]: https://img.shields.io/github/issues/nfephp-org/sped-nfse-ginfes.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/nfephp-org/sped-nfse-ginfes/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/nfephp-org/sped-nfse-ginfes.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/nfephp-org/sped-nfse-ginfes.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/nfephp-org/sped-nfse-ginfes.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/nfephp-org/sped-nfse-ginfes.svg?style=flat-square
[ico-license]: https://poser.pugx.org/nfephp-org/nfephp/license.svg?style=flat-square
[ico-gitter]: https://img.shields.io/badge/GITTER-4%20users%20online-green.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/nfephp-org/sped-nfse-ginfes
[link-travis]: https://travis-ci.org/nfephp-org/sped-nfse-ginfes
[link-scrutinizer]: https://scrutinizer-ci.com/g/nfephp-org/sped-nfse-ginfes/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/nfephp-org/sped-nfse-ginfes
[link-downloads]: https://packagist.org/packages/nfephp-org/sped-nfse-ginfes
[link-author]: https://github.com/nfephp-org
[link-issues]: https://github.com/nfephp-org/sped-nfse-ginfes/issues
[link-forks]: https://github.com/nfephp-org/sped-nfse-ginfes/network
[link-stars]: https://github.com/nfephp-org/sped-nfse-ginfes/stargazers
[link-gitter]: https://gitter.im/nfephp-org/sped-nfse-ginfes?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge