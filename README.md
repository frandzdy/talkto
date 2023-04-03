# Secret

-------------------------------------------------------------------------------

## Installation

### Requirements

* Git
* Docker CE
* Latest Node LTS
* Latest stable version of Yarn

### Import

Clone the project https://github.com/frandzdy/loue

### Project Configuration and Dependency Installation

**Environment setup**

- Create `.env.local` configuration file
- Set needed variables

**Build container, start-project and install dependencies**

Run the following command from the project root:

```sh
make docker-start
make install
```
