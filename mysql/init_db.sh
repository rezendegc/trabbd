#!/bin/bash
echo "CRIANDO BANCO DE DADOS"
# SEED RELEVANCIA 1
file="/var/local/lista_relevancia_1.txt"
ids="("
while IFS= read line
do
	ids+="'${line}',"
done <"$file"
ids+="'${line}',"
ids="${ids%,})"
query="UPDATE \`trabbd\`.\`users\` SET \`relevancia\`='2' WHERE \`id\` IN ${ids};"
mysql -u root -proot < /var/local/seed.sql #cria a tabela

mysql -u root -proot <<< $query


# SEED RELEVANCIA 2
file2="/var/local/lista_relevancia_2.txt"
ids2="("
while IFS= read line2
do
	ids2+="'${line2}',"
done <"$file2"
ids2+="'${line2}',"
ids2="${ids2%,})"
query2="UPDATE \`trabbd\`.\`users\` SET \`relevancia\`='1' WHERE \`id\` IN ${ids2};"
mysql -u root -proot <<< $query2