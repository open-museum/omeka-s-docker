# Omeka S Dockerized

Omeka S is a web publishing platform for digital cultural heritage collections.

[![GitHub issues](https://img.shields.io/github/issues/maehr/omeka-s-docker.svg)](https://github.com/maehr/omeka-s-docker/issues)
[![GitHub forks](https://img.shields.io/github/forks/maehr/omeka-s-docker.svg)](https://github.com/maehr/omeka-s-docker/network)
[![GitHub stars](https://img.shields.io/github/stars/maehr/omeka-s-docker.svg)](https://github.com/maehr/omeka-s-docker/stargazers)
[![GitHub license](https://img.shields.io/github/license/maehr/omeka-s-docker.svg)](https://github.com/maehr/omeka-s-docker/blob/master/LICENSE.md)

## Installation

Install [Docker](https://www.docker.com/).

Copy and edit `.env.example`

```bash
cp .env.example .env
nano .env
```

Create folders and `database.ini`

```bash
bash setup.sh
```

## Usage

Start Omeka S

```bash
docker-compose up -d
```

Start Omeka S with Letsencrypt

```bash
docker-compose -f docker-compose.letsencrypt.yml up -d
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
