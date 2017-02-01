# SQLCheckExpect
PHP framework to check the expected results from writes to a MYSQL database


selectConnectionCredentials() - SELECT 
insertConnectionCredentials() - SELECT & INSERT
updateConnectionCredentials() - SELECT & UPDATE
deleteConnectionCredentials() - SELECT & DELETE

connectionString() - returns $connection from mysqli_connect()

select_sqli(string $select_query) - returns boolean $result from mysqli_connect()
select_sqliLog(string $select_query,int $expectedResult) - returns boolean $result from mysqli_connect()
select_sqliTransaction(string $select_query,int $expectedResult) - returns boolean $result from mysqli_connect()

insert_sqli(string $insert_query) - returns boolean $result from mysqli_connect()
insert_sqliLog(string $insert_query(string $table,int $expectedResult) - returns boolean $result from mysqli_connect()
insert_sqliTransaction(string $insert_query,string $table,int $expectedResult) - returns boolean $result from mysqli_connect()

update_sqli(string $update_query) - returns boolean $result from mysqli_connect()
update_sqliLog(string $update_query,string $table,int $expectedResult) - returns boolean $result from mysqli_connect()
update_sqliTransaction(string $update_query,string $table,int $expectedResult) - returns boolean $result from mysqli_connect()

delete_sqli(string $dlete_query) - returns boolean $result from mysqli_connect()
delete_sqliLog(string $delete_query,string $table,int $expectedResult) - returns boolean $result from mysqli_connect()
delete_sqliTransaction(string $delete_query,string $table,int $expectedResult) - returns boolean $result from mysqli_connect()