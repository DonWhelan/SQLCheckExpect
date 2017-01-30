# SQLCheckExpect
PHP framework to check the expected results from writes to a MYSQL database


selectConnectionCredentials() - SELECT 
insertConnectionCredentials() - SELECT & INSERT
updateConnectionCredentials() - SELECT & UPDATE
deleteConnectionCredentials() - SELECT & DELETE

connectionString() - returns $connection from mysqli_connect()

select_query(string $select_query) - returns boolean $result from mysqli_connect()

select_queryE(string $select_query,int $expectedResult) - returns boolean $result from mysqli_connect()

insert_query(string $insert_query) - returns boolean $result from mysqli_connect()

insert_queryE(string $insert_query,string $table,int $expectedResult) - returns boolean $result from mysqli_connect()

update_query(string $update_query) - returns boolean $result from mysqli_connect()

update_queryE(string $update_query,string $table,int $expectedResult) - returns boolean $result from mysqli_connect()

delete_query(string $dlete_query) - returns boolean $result from mysqli_connect()

delete_queryE(string $delete_query,string $table,int $expectedResult) - returns boolean $result from mysqli_connect()
