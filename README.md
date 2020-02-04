# sped-nfse-ginfes

[![Join the chat at https://gitter.im/nfephp-org/sped-nfse-ginfes](https://badges.gitter.im/nfephp-org/sped-nfse-ginfes.svg)](https://gitter.im/nfephp-org/sped-nfse-nacional?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Api para comunicação com webservices do Projeto NFSe Ginfes

## Esta API esta sendo testada para Porto Alegre (apenas) e está em BETHA TESTES

*Utilize o chat do Gitter para iniciar discussões especificas sobre o desenvolvimento deste pacote.*

[![Chat][ico-gitter]][link-gitter]

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

## Cidades já atendidas (podem ainda ser necessários alguns ajustes)

- Recife (PE)
- Belo Horizonte (MG)
- Juiz de Fora (MG)
- Rio de Janeiro (RJ)
- Angra dos Reis (RJ)
- Duque de Caxias (RJ)
- Itaguaí (RJ)
- Macaé (RJ)
- Mangaratiba (RJ)
- Niteroí (RJ)
- Rio das Ostras (RJ)
- Americana (SP)
- Porto Alegre (RS)

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