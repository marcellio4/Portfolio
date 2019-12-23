<?php
require __DIR__ . "/../classes/Validation.php";
require_once __DIR__ . "/../include/functions.php";
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase {
    
    private static $validation;
    
    public function setUp() :void {
        self::$validation = new Validation();
    }
    
    public function testSpecialCharacters() {
        $checkChar = self::$validation->hasSpecialCharacters('', 'hello world');
        $this->assertTrue($checkChar);
    }
    
    public function testIsEmpty() {
        $empty = self::$validation->isEmpty('', '');
        $this->assertFalse($empty);
    }
    
    public function testEmail() {
        $mail = self::$validation->email('', 'm@google.com');
        $this->assertTrue($mail);
    }
    
    public function testIsAlphabetical() {
        $alpha = self::$validation->isAlphabetical('', 'the');
        $this->assertTrue($alpha);
    }
    
    public function testGetErrorsArr() {
        $arr = self::$validation->getErrorsArr();
        $this->assertIsArray($arr);
    }
    
    public function testTitles() {
        $title = self::$validation->Titles('', 'Mr');
        $this->assertTrue($title);
    }
    
    public function testIsErrorsDetected() {
        $this->assertFalse(self::$validation->isErrorsDetected());
    }
    
    public function testCheckLength() {
        $str = self::$validation->checkLength('hello', 2, 5);
        $this->assertTrue($str);
    }
    
    public function testSame() {
        $str = self::$validation->same(clean_str('marcellio01'), clean_str('marcellio01'));
        $this->assertTrue($str);
    }
    
    public function testPassword(){
        $bool = self::$validation->password('pass', 'llo23ghhh34');
        $this->assertTrue($bool);
    }
}
