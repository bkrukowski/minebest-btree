# Binary tree

## Create tables in database

```
bin/console doctrine:schema:create
```

## Generate random tree and save to file

```
bin/console tree:generate-random --depth 8 -v -o data/example.yaml
```

## Import tree from file to database

```
bin/console tree:import data/example.yaml
```

## Dev server

```
bin/console server:run
```

## Database

### Schema

```bash
bin/console doctrine:schema:create --dump-sql
```

```mysql
     CREATE TABLE node (id INT NOT NULL, left_node_id INT DEFAULT NULL, right_node_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, left_credits INT NOT NULL, right_credits INT NOT NULL, is_root TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_857FE84551DF9AB1 (left_node_id), UNIQUE INDEX UNIQ_857FE84521D02DF1 (right_node_id), INDEX is_root (is_root), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     ALTER TABLE node ADD CONSTRAINT FK_857FE84551DF9AB1 FOREIGN KEY (left_node_id) REFERENCES node (id);
     ALTER TABLE node ADD CONSTRAINT FK_857FE84521D02DF1 FOREIGN KEY (right_node_id) REFERENCES node (id);
```

## Code standards

[@see](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

```
php-cs-fixer fix src --rules=@Symfony,@PSR1,native_constant_invocation,native_function_invocation,declare_strict_types --allow-risky=yes
```
