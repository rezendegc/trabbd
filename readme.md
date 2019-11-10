# Introdução

Trabalho realizado pelos alunos **Gabriel Castro de Rezende** e **Beatriz Ogioni Sessa** para a disciplina de Banco de Dados 2019/2, ministrada pelo professor Rodrigo Laiola.
O trabalho consiste em criar um banco de dados que consiga suportar uma grande quantidade de dados ( +8 milhões de registos ) e analisar as diferentes possibilidades de otimização.

## Como rodar

O trabalho foi feito utilizado o sistema Linux, com a distribuição Ubuntu 18.04
Para rodar, você deve ter instalado o docker e docker-compose (caso ainda não tenha, siga [esse tutorial](https://cwiki.apache.org/confluence/pages/viewpage.action?pageId=94798094))
Em seguida, baixe o arquivo com os registros do banco de dados ([aqui](https://s3.amazonaws.com/careers-picpay/users.csv.gz)) e extraia para a pasta "mysql" com o nome "users.csv". Deve-se salvar o arquivo com apenas '\n', dado que o arquivo baixado do site terá \n e \r.
Por último, rode o comando `docker-compose up` e aguarde (quando uma mensagem terminada em `Socket: '/var/run/mysqld/mysqlx.sock' bind-address: '::' port: 33060` aparecer, significa que está tudo pronto para uso.
Agora, o servidor estará disponível no localhost com a porta 80, e o banco de dados com a porta 3306.

## Resultados e Considerações

Primeiramente, gostaria de ressaltar que todos os dados e tempos obtidos que serão apresentados a partir de agora foram feitos em uma máquina virtual Ubuntu com ~8gb de RAM e um armazenamento SSD, com apenas as primeiras 1 milhão de linhas do arquivo "users.csv" disponibilizado.

Para importar o arquivo foi utilizado o comando a seguir, que carrega todas as linhas de um CSV na tabela especificada.

> LOAD DATA INFILE '/var/local/users.csv' IGNORE
> INTO TABLE `trabbd`.`users`
> FIELDS TERMINATED BY ','
> OPTIONALLY ENCLOSED BY '"'
> LINES TERMINATED BY '\n';

O primeiro desafio foi em tentar otimizar o tempo que demora para os registros serem inseridos no banco de dados. Com a engine padrão (InnoDB) e a tabela sem nenhum índice extra demorava aproximadamente 2 minutos e 45 segundos para popular o banco de dados e subir os containers.
Ao utilizar a engine `MyISAM`, esse tempo foi reduzido para apenas 30 segundos.
A explicação para isso é porque o MyISAM apenas coloca os registros na memória do servidor e libera a execução e deixa para o servidor decidir quando será o melhor momento para terminar de inserir os dados no disco. Já o InnoDB força o servidor a colocar todas as informações no disco (além de fazer algumas operações extras), o que é muito mais custoso em um primeiro momento. A desvantagem óbvia do MyISAM é que se o servidor crashar por algum motivo antes do servidor decidir colocar todos os dados em disco, os dados não serão inseridos completamente.

O próximo ponto a se otimizar foi a query em si. Para as relevâncias foi criada uma coluna "relevancias" que tem o valor 0 caso não tenha relevancia, 2 caso esteja na lista de relevancia 1 e 1 caso esteja na lista de relevancia 2 (assim podemos ordenar de forma decrescente por relevancia).

Serão primeiro apresentados os resultados e no final feitas as devidas discussões.

Para não haver muitas repetições, a partir de agora as queries serão nomeadas:

> `(Query 1)` SELECT \* FROM trabbd.users WHERE name like '%a%' or username like '%a%' ORDER BY relevancia DESC, name, username LIMIT 15;

> `(Query 2)` SELECT \* FROM trabbd.users WHERE name like '%gabr%' or username like '%gabr%' ORDER BY relevancia DESC, name, username LIMIT 15;`

> `(Query 3)` SELECT \* FROM trabbd.users WHERE name like '%monica fr%' or username like '%monica fr%' ORDER BY relevancia DESC, name, username LIMIT 15;`

> `(Query 4)` SELECT \* FROM trabbd.users WHERE name like 'a%' or username like 'a%' ORDER BY relevancia DESC, name, username LIMIT 15;

> `(Query 5)` SELECT \* FROM trabbd.users WHERE name like 'gabr%' or username like 'gabr%' ORDER BY relevancia DESC, name, username LIMIT 15;

> `(Query 6)` SELECT \* FROM trabbd.users WHERE name like 'monica fr%' or username like 'monica fr%' ORDER BY relevancia DESC, name, username LIMIT 15;

> `(Query 7)` SELECT _ FROM trabbd.users WHERE MATCH(name, username) AGAINST ('+a_' IN BOOLEAN MODE) ORDER BY relevancia DESC, name, username LIMIT 15;

> `(Query 8)` SELECT _ FROM trabbd.users WHERE MATCH(name, username) AGAINST ('+gabr_' IN BOOLEAN MODE) ORDER BY relevancia DESC, name, username LIMIT 15;

> `(Query 9)` SELECT _ FROM trabbd.users WHERE MATCH(name, username) AGAINST ('+monica +fr_' IN BOOLEAN MODE) ORDER BY relevancia DESC, name, username LIMIT 15;

#### Sem indíces

Sem nenhum índice o banco de dados possuia ~688MB.

- A Query 1 demorou uma média de 1150ms para ser executada;
- A Query 2 demorou uma média de 950ms para ser executada;
- A Query 3 demorou uma média de 1000ms para ser executada;
- A Query 4 demorou uma média de 800ms para ser executada;
- A Query 5 demorou uma média de 750ms para ser executada;
- A Query 6 demorou uma média de 750ms para ser executada;
- A Query 7 demorou uma média de 2800ms para ser executada;
- A Query 8 demorou uma média de 2800ms para ser executada;
- A Query 9 demorou uma média de 2800ms para ser executada.

### Com índice normais

Aqui, serão utilizados os índices "normais" para os campos de username e name, da seguinte forma:

> INDEX `name_INDEX` (`name`)
> INDEX `username_INDEX` (`username`)

O tempo para subir o banco de dados e os containers aumentou um pouco (em 10 segundos) e o tamanho do bd aumentou para ~719MB.

- A Query 1 demorou uma média de 1200ms para ser executada;
- A Query 2 demorou uma média de 1100ms para ser executada;
- A Query 3 demorou uma média de 1050ms para ser executada;
- A Query 4 demorou uma média de 600ms para ser executada;
- A Query 5 demorou uma média de 30ms para ser executada;
- A Query 6 demorou uma média de 1.5ms para ser executada;
- A Query 7 demorou uma média de 3000ms para ser executada;
- A Query 8 demorou uma média de 2800ms para ser executada;
- A Query 9 demorou uma média de 2800ms para ser executada.

### Com índice fulltext

Aqui será utilizado um índice fulltext em cima dos campos username e name, da seguinte forma:

> FULLTEXT(`username`,`name`)

O tempo para subir o banco de dados e os containers aumentou em 15 segundos se comparado com o índice anterior (e 25 segundos se comparado com sem índice) e o tamanho no bd aumentou para ~721MB.

- A Query 1 demorou uma média de 1200ms para ser executada;
- A Query 2 demorou uma média de 1100ms para ser executada;
- A Query 3 demorou uma média de 1050ms para ser executada;
- A Query 4 demorou uma média de 800ms para ser executada;
- A Query 5 demorou uma média de 750ms para ser executada;
- A Query 6 demorou uma média de 750ms para ser executada;
- A Query 7 demorou uma média de 3000ms para ser executada;
- A Query 8 demorou uma média de 100ms para ser executada;
- A Query 9 demorou uma média de 10ms para ser executada.

### Análise

Primeiramente vale ressaltar que as queries apresentadas, terão resultados diferentes. As três primeiras irão encontrar qualquer substring com o argumento dado. As queries 4, 5 e 6 irão encontrar strings que comecem com o argumento dado. As queries 7, 8 e 9 irão encontrar palavras dentro da string que comecem com o argumento dado. Então, se passarmos "gabr", as queries 1, 2, 3, 7, 8 e 9 encontrariam o campo "João Gabriel", porém as queries 4, 5 e 6 não. Isso foi levado em conta para decidir qual índice usar.

Pudemos perceber que as queries 1, 2 e 3 não puderam ser otimizadas independente do índice utilizado. Isso acontece pois o mysql não consegue otimizar as pesquisas quando o operador wildcard (%) está no começo da string, pois a consulta terá que percorrer toda a string em todos os casos. Assim, essa forma de montar a query (com wildcard antes e depois da palavra pesquisada) não será utilizada.

Com as queries 4, 5 e 6 temos o operador wildcard apenas no final da palavra. Assim, pudemos perceber uma diferença enorme (principalmente nas queries 5 e 6) entre usar índice e não usar. O mysql conseguiu otimizar esse tipo de query apenas quando utilizado o índice "normal" do banco, ao utilizar o índice fulltext não ocorreu nenhuma otimização.

Com as queries 7, 8 e 9 foi utilizada outra sintaxe, onde o operador '+' indica que a palavra tem que estar presente no campo e o operador '\*' é o wildcard. Assim, pudemos perceber que as queries 8 e 9 ficaram bem otimizadas com o índice fulltext e, apesar de ser mais lenta que as queries 5 e 6, ainda é preferível pois retornam resultados melhores, dado que identificam palavras no meio do texto, desde que as palavras comecem com o argumento dado. A Query 7 (onde se passa apenas uma letra como argumento) não conseguiu ser otimizada.

Levando tudo em conta, optamos por utilizar o índice fulltext pois o espaço em disco foi praticamente o mesmo do outro tipo de índice e o resultado das pesquisas é mais satisfatório (apesar de um pouco mais lento).

Vale ressaltar que testamos colocar um índice ordenado no campo "relevancia", porém não teve impacto no tempo da query, então optamos por removê-lo. A engine MyISAM não permite índices ordenados por DESC, então invertemos a ordem da relevância (0 sendo mais importante e 2 o menos importante) fazer este teste.

Por último, decidimos otimizar a paginação. Normalmente, esta é feita por meio do offset, passando-se no final da query qual o tamanho do offset e quantas tuplas queremos, como por exemplo: `LIMIT BY 5,5` que irá pular os 5 primeiros registros e retornar os próximos 5. Porém, quando chegamos ao final da lista, ao pular 150000 registros o tempo da query aumentou drasticamente, chegando a 5000ms (contra 3000ms sem o limit, ou no começo do mesmo), pois o banco acabava tendo que passar por todos os registros até chegar no offset que definimos. Então, decimidos utilizar outro método: mantivemos a lista ordenada por nome, sobrenome e relevancia e para pedir a próxima página apenas passamos o último nome, sobrenome e relevancia que recebemos. Assim, podemos colocar como condição de where na query o seguinte: `(name, username) > (nome_recebido, username_recebido) AND relevancia <= (relevancia_recebida)`. Desta forma, o banco conseguia ir direto para o registro que queríamos e o tempo de query permaneceu constante em 3000ms, independente da página.
