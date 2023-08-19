# Installation

### Clone the Repository

| Type | URL |
| -- | -- |
| ssh | `git clone git@git.nexlabs.co:community-voice/Web.git` |
| https | `git clone https://git.nexlabs.co/community-voice/Web.git` |

### Create .env
- Copy from [.env.example](.env.example)

### Install Composer Packages
```shell
composer install
```

### Add Tables
```shell
php artisan migrate:fresh --seed
```

### Make Public Folder Accessible
```shell
php artisan storage:link
```

### Add Virtual Hosts
- admin.communityvoice.local
- api.communityvoice.local

#### `That's all, now run the project!`
___

### Login Credentials

| Type | Email | Password |
| -- | -- | -- |
| Root | admin@communityvoice.com | password |

___

### Postman Links
- Download collection [here](https://www.getpostman.com/collections/8a119c42dcfee4753550link)
- Check api documentation [here](https://documenter.getpostman.com/view/790629/SW7Z3U3i)

___

### Tool Requirements

| Title | Version |
| -- | -- |
| Nginx | 1.16.1 |
| PHP | 7.3.11 |
| MySQL | 8.0.19 |
| Composer | 1.9.0 |
