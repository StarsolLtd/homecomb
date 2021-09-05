# HomeComb

## Requirements
- GNU make: https://www.gnu.org/software/make/
- Docker https://docs.docker.com/engine/installation/
- Docker compose https://docs.docker.com/compose/install/

## First time setup
`make build`

## Run tests
`make php-test`
`make npm-test`
`make e2e`

## Xebug
On Linux environments, add the following to `/etc/hosts`
```
172.17.0.1 host.docker.internal
```
This should not be needed on Mac or Windows.