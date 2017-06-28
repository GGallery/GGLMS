<?php
/**
 * @brief The exceptions:: class extends the PHP class Exception.
 * It wants to be a replacement of @see Exception, adding tracking capabilities and source code captureing where the exception is thrown.
 *
 * Example of use with @see debug::
 * <code>
 * try {
 *    try {
 *       throw new exceptions('First exception', E_USER_ERROR);
 *    } catch (Exception $e) {
 *       throw new exceptions('Second exception', E_WARNING, $e);
 *    }
 * } catch (Exception $e) {
 *    debug::exception($e, true, true, 11);
 * }
 * </code>
 * Another example:
 * <code>
 * function div($a, $b) {
 *    if (!$b)
 *       throw new exceptions('Division by zero', E_USER_ERROR);
 *    return $a/$b;
 * }
 * try {
 *    $n1 = 4; $n2 = 0;
 *    $result = div($n1, $n2);
 * } catch (Exception $e) {
 *    throw new exceptions('Error in script execution', E_USER_ERROR, $e);
 * }
 * </code>
 *
 * {@link http://www.php.net/manual/en/spl.exceptions.php SPL Exceptions} provides a set of standard Exceptions:
 * - BadFunctionCallException:: Exception thrown if a callback refers to an undefined function or if some arguments are missing.
 * - BadMethodCallException:: Exception thrown if a callback refers to an undefined method or if some arguments are missing.
 * - DomainException:: Exception thrown if a value does not adhere to a defined valid data domain (if($divisor == 0)).
 * - InvalidArgumentException:: Exception thrown if an argument does not match with the expected value (if(!is_int($int))).
 * - LengthException:: Exception thrown if a length is invalid.
 * - LogicException:: Exception that represents error in the program logic. This kind of exceptions should directly lead to a fix in your code. (BadFunctionCallException, DomainException, InvalidArgumentException, LengthException, OutOfRangeException are sub-classes of LogicException).
 * - OutOfBoundsException:: Exception thrown if a value is not a valid key. This represents errors that cannot be detected at compile time.
 * - OutOfRangeException:: Exception thrown when an illegal index was requested. This represents errors that should be detected at compile time.
 * - OverflowException:: Exception thrown when adding an element to a full container.
 * - RangeException:: Exception thrown to indicate range errors during program execution. Normally this means there was an arithmetic error other than under/overflow. This is the runtime version of DomainException.
 * - RuntimeException:: Exception thrown if an error which can only be found on runtime occurs. (OutOfBoundsException, OverflowException, RangeException, UnderflowException, UnexpectedValueException are sub-classes of RuntimeException).
 * - UnderflowException:: Exception thrown when performing an invalid operation on an empty container, such as removing an element.
 * - UnexpectedValueException:: Exception thrown if a value does not match with a set of values. Typically this happens when a function calls another function and expects the return value to be of a certain type or value not including arithmetic or buffer related errors.
 *
 * @package ErrorHandler
 */
class exceptions extends Exception {

	/**
	 * Costructor
	 *
	 * @param string $message Error message (default 'Unknow message').
	 * @param int $code Code error message (default 0).
	 * @param exceptions $parent Parent except in case of nested exceptions (default null).
	 * @param int $line Used to transform a trigger_error into an exception (default null).
	 * @param string $file Used to transform a trigger_error into an exception (default null).
	 */
	public function __construct($message=null, $code=0, Exception $parent=null, $line=null, $file=null) {
		parent::__construct($message, $code, $parent);
		if (!empty($line)) {
			$this->line = $line;
		}
		if (!empty($file)) {
			$this->file = $file;
		}
	}

	/**
	 * Destructor
	 */
	public function __destruct() {}

	/**
	 * Gets a portion of the n lines of code taking n/2 above and n/2 below the line where the exception was generated.
	 * Returns an array containing the lines of code.
	 *
	 * @param int $n Number of lines taken (default 11).
	 * @return array
	 */
	public function get_source($n=11) {
		if (!is_readable($this->file)) return array();
		$n = filter_var($n, FILTER_VALIDATE_INT, array('options' => array('min_range'=>1)));
		if (!$n) return array();
		$n_2 = floor($n/2) + 1;
		return array_map('trim', array_slice(file($this->file), (($this->line-$n_2)<0)?0:($this->line-$n_2), $n, false));
	}

	/**
	 * Like @see get_source() returns the lines of code where the exception was thrown, but as a string.
	 * Each line is separated by PHP_EOL.
	 *
	 * @param int $n Number of lines taken (default 11).
	 * @return string
	 */
	public function get_source_str($n=11) {
		return join(PHP_EOL, $this->get_source($n));
	}

	/**
	 * (non-PHPdoc)
	 * @see Exception::__toString()
	 */
	public function __toString() {
		return 'exception \'exceptions\' with message \'['.$this->code.']: '.$this->message. '\' in '.$this->file.':'.$this->line;
	}
}
// ~@:-]
?>
