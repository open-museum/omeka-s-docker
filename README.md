# Omeka S Docker

[Omeka S](https://github.com/omeka/omeka-s/) is a web publishing platform for digital cultural heritage collections.

[![GitHub issues](https://img.shields.io/github/issues/maehr/omeka-s-docker.svg)](https://github.com/maehr/omeka-s-docker/issues)
[![GitHub forks](https://img.shields.io/github/forks/maehr/omeka-s-docker.svg)](https://github.com/maehr/omeka-s-docker/network)
[![GitHub stars](https://img.shields.io/github/stars/maehr/omeka-s-docker.svg)](https://github.com/maehr/omeka-s-docker/stargazers)
[![GitHub license](https://img.shields.io/github/license/maehr/omeka-s-docker.svg)](https://github.com/maehr/omeka-s-docker/blob/master/LICENSE.md)

## Installation

Install [Docker](https://www.docker.com/).

Go to `example`

Copy and edit `.env.example`

```bash
cd example
cp .env.example .env
nano .env
```

Export environment variables and use `setup.sh` to create `database.ini` and set permissions

```bash
cd example
source .env
bash setup.sh
```

If you want to start a local development instance, use `docker-compose.dev.yml`.

```bash
cd example
docker-compose --file docker-compose.dev.yml up -d
```

## Usage

Start Omeka S

```bash
cd example
docker-compose up -d
```

If your installation complains about not being able to write to the folder `files`, fix permissions accordingly.

```bash
cd example
chown -R www-data:www-data files logs modules themes
```

Stop Omeka S

```bash
cd example
docker-compose up -d
```

Install [EasyInstall](https://github.com/Daniel-KM/Omeka-S-module-EasyInstall) to make your life more easy

```bash
cd example
cd modules
curl -L -o EasyInstall-3.2.5.zip "https://github.com/Daniel-KM/Omeka-S-module-EasyInstall/releases/download/3.2.5/EasyInstall-3.2.5.zip"
unzip EasyInstall-3.2.5.zip
rm EasyInstall-3.2.5.zip
```

## Support

This project is maintained by [@maehr](https://github.com/maehr). Please understand that we won't be able to provide individual support via email. We also believe that help is much more valuable if it's shared publicly, so that more people can benefit from it.

| Type                   | Platforms                                                    |
| ---------------------- | ------------------------------------------------------------ |
| 🚨 **Bug Reports**      | [GitHub Issue Tracker](https://github.com/maehr/omeka-s-docker/issues) |
| 🎁 **Feature Requests** | [GitHub Issue Tracker](https://github.com/maehr/omeka-s-docker/issues) |
| 🛡 **Report a security vulnerability**      | [GitHub Issue Tracker](https://github.com/maehr/omeka-s-docker/issues) |

## Roadmap

No changes are currently planned.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/maehr/omeka-s-docker/tags).

## Authors and acknowledgment

- **Moritz Mähr** - _Initial work_ - [maehr](https://github.com/maehr)

See also the list of [contributors](https://github.com/maehr/omeka-s-docker/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
