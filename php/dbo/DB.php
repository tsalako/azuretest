<?php
	
class DB{
	private $dbhost = 'eu-cdbr-azure-west-b.cloudapp.net';
	private $dbuser = 'b8833e255abd25';
	private $dbpass = 'f25e32ab';
	private $dbname = 'llamadb';
	private $db = null;

	/** \brief DB constructor
	 * \details this is the constructor for the database. This will connect to the database
	 * via calling an private connect function. 
	 */
	function __construct(){
		$this->connectToDatabase();
	}
	/** \brief DB connection 
	 * \details contains the connection details to connect to our database. we have to 
	 * create a connection using the host, uname, pass, database private variables. We 
	 * also specify some implementation details. We turn off prepare emulation which needs
	 * to be turned off in order to use PDO safely. We also enable exception mode. This 
	 * way we do not use 'or die' which is unsafe. We can catch the exception using 
	 * PDOException. This will configure all statements to throw exceptions later
	 */
	private function connectToDatabase(){
		$this->db = new PDO('mysql:host='.$this->dbhost.';dbname='.$this->dbname.';charset=utf8', $this->dbuser, $this->dbpass);
		$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 
	}
	
	/** \brief prepares a statement for execution and returns a statement object
	 * \details Prepares an SQL statement to be executed by the PDOStatement::execute() 
	 * method. The SQL statement can contain zero or more named (:name) or question mark 
	 * (?) parameter markers for which real values will be substituted when the statement 
	 * is executed. You cannot use both named and question mark parameter markers within 
	 * the same SQL statement; pick one or the other parameter style. Use these parameters
	 * to bind any user-input, do not include the user-input directly in the query.
	 *
	 * You must include a unique parameter marker for each value you wish to pass in to 
	 * the statement when you call PDOStatement::execute(). You cannot use a named 
	 * parameter marker of the same name twice in a prepared statement. You cannot bind 
	 * multiple values to a single named parameter in, for example, the IN() clause of an 
	 * SQL statement.
	 *
	 * Calling PDO::prepare() and PDOStatement::execute() for statements that will be 
	 * issued multiple times with different parameter values optimizes the performance of
	 * your application by allowing the driver to negotiate client and/or server side 
	 * caching of the query plan and meta information, and helps to prevent SQL injection
	 * attacks by eliminating the need to manually quote the parameters.
	 *
	 * PDO will emulate prepared statements/bound parameters for drivers that do not 
	 * natively support them, and can also rewrite named or question mark style parameter 
	 * markers to something more appropriate, if the driver supports one style but not the
	 * other.
	 *
	 * \param statement this must be a valid SQL statement for mySQL database server. It 
	 * must be a string
	 * \param driver_options This array holds one or more key=>value pairs to set 
	 * attribute values for the PDOStatement object that this method returns. You would 
	 * most commonly use this to set the PDO::ATTR_CURSOR value to PDO::CURSOR_SCROLL to 
	 * request a scrollable cursor. Some drivers have driver specific options that may be 
	 * set at prepare-time. Must be an array.
	 * 
	 * \return If the database server successfully prepares the statement, PDO::prepare() 
	 * returns a PDOStatement object. If the database server cannot successfully prepare 
	 * the statement, PDO::prepare() emits PDOException (depending on error handling).
	 *
	 * \remarks most of this documentation was taken directly from 
	 * http://www.php.net/manual/en/pdo.prepare.php
	 */
	 
	public function prepare($statement, $driver_options = array()){
		 return $this->db->prepare($statement,$driver_options);
	}
	
	/** \brief Initiates a transaction
	 * \details Turns off autocommit mode. While autocommit mode is turned off, changes 
	 * made to the database via the PDO object instance are not committed until you end 
	 * the transaction by calling PDO::commit(). Calling PDO::rollBack() will roll back 
	 * all changes to the database and return the connection to autocommit mode.

	 * Some databases, including MySQL, automatically issue an implicit COMMIT when a
	 * database definition language (DDL) statement such as DROP TABLE or CREATE TABLE
	 * is issued within a transaction. The implicit COMMIT will prevent you from rolling
	 * back any other changes within the transaction boundary.
	 *
	 * \return Returns TRUE on success or FALSE on failure
	 *
	 * \remarks most of this documentation was taken directly from 
	 * http://www.php.net/manual/en/pdo.beginTransaction.php
	 */
	 
	public function begin(){
		return $this->db->beginTransaction();
	}
	
	/** \brief Commits a transaction
	 * \details Commits a transaction, returning the database connection to autocommit
	 * mode until the next call to PDO::beginTransaction() starts a new transaction.
	 *
	 * \return Returns TRUE on success or FALSE on failure
	 *
	 * \remarks most of this documentation was taken directly from 
	 * http://www.php.net/manual/en/pdo.prepare.php
	 */
	
	public function commit(){
		return $this->db->commit();
	}
	
	/** \brief Rolls back a transaction
	 * \details Rolls back the current transaction, as initiated by
	 * PDO::beginTransaction(). A PDOException will be thrown if no transaction is active.
	 *
	 * If the database was set to autocommit mode, this function will restore autocommit
	 * mode after it has rolled back the transaction.
	 *
	 * \return Returns true on success or FALSE on failure
	 *
	 * \remarks most of this documentation was taken directly from 
	 * http://www.php.net/manual/en/pdo.commit.php
	 */

	public function rollback(){
		return $this->db->rollBack();
	}
	
	/** \brief Fetch the SQLSTATE associated with the ;ast operation on the database
	 *
	 * \returns Returns an SQLSTATE, a five characters alphanumeric identifier defined in
	 * the ANSI SQL-92 standard. Briefly, an SQLSTATE consists of a two characters class
	 * value followed by a three characters subclass value. A class value of 01 indicates
	 * a warning and is accompanied by a return code of SQL_SUCCESS_WITH_INFO. Class
	 * values other than '01', except for the class 'IM', indicate an error. The class
	 * 'IM' is specific to warnings and errors that derive from the implementation of PDO
	 * (or perhaps ODBC, if you're using the ODBC driver) itself. The subclass value '000'
	 * in any class indicates that there is no subclass for that SQLSTATE.
	 *
	 * PDO::errorCode() only retrieves error codes for operations performed directly on
	 * the database handle. If you create a PDOStatement object through PDO::prepare() or
	 * PDO::query() and invoke an error on the statement handle, PDO::errorCode() will
	 * not reflect that error. You must call PDOStatement::errorCode() to return the error
	 * code for an operation performed on a particular statement handle.
	 *
	 * Returns NULL if no operation has been run on the database handle.
	 */

	public function errorCode(){
		return $this->db->errorCode();
	}
	
	/** \brief Fetch extended error information associated with the last operation on the
	 * database
	 *
	 * \return see http://www.php.net/manual/en/pdo.errorinfo.php for information
	 */
	public function errorInfo(){
		return $this->db->errorInfo();
	}
	
	/** \briefs Execute an SQL statement and return the number of affected rows
	 * \details PDO::exec() executes an SQL statement in a single function call, returning
	 * the number of rows affected by the statement.
	 *
	 * PDO::exec() does not return results from a SELECT statement. For a SELECT statement
	 * that you only need to issue once during your program, consider issuing
	 * PDO::query(). For a statement that you need to issue multiple times, prepare a
	 * PDOStatement object with PDO::prepare() and issue the statement with
	 * PDOStatement::execute().
	 *
	 * \param statement The SQL statement to prepare and execute. Data inside the query
	 * should be properly escaped
	 *
	 * \return PDO::exec() returns the number of rows that were modified or deleted by the
	 * SQL statement you issued. If no rows were affected, PDO::exec() returns 0.
	 */
	public function exec($statement){
		return $this->db->exec($statement);
	}
	
	/** \brief Quotes a string for use in a query
	 * \details PDO::quote() places quotes around the input string (if required) and
	 * escapes special characters within the input string, using a quoting style
	 * appropriate to the underlying driver.
	 *
	 * If you are using this function to build SQL statements, you are strongly 
	 * recommended to use PDO::prepare() to prepare SQL statements with bound parameters
	 * instead of using PDO::quote() to interpolate user input into an SQL statement.
	 * Prepared statements with bound parameters are not only more portable, more
	 * convenient, immune to SQL injection, but are often much faster to execute than
	 * interpolated queries, as both the server and client side can cache a compiled
	 * form of the query.
	 *
	 * \param string the string to be quoted
	 * \param parameter_type Provides a data type hint for drivers that have alternative
	 * quoting styles
	 *
	 * \return Returns a quoted string that is theoretically safe to pass into an SQL 
	 * statement. Returns FALSE if the driver does not support quoting in this way.
	 */
	public function quote($string, $parameter_type = PDO::PARAM_STR){
		return $db->quote($string, $parameter_type);
	}
	
	public function query($statement) {
		echo 'in DB query <br />';
		return $this->db->query($statement);
	}
}
	

  	
?>