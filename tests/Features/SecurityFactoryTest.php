<?php

namespace MayMeow\Tests\Features;

use MayMeow\Tests\TestCase;
use MayMeow\Factory\CertificateFactory;
use MayMeow\Factory\SecurityFactory;
use MayMeow\Loaders\KeyPairFileLoader;

class SecurityFactoryTest extends TestCase 
{
    protected const KEY_PAIR_NAME = 'keys-2';

    /**
     * @var SecurityFactory $sf;
     */
    protected $sf;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->sf = new SecurityFactory(new CertificateFactory());
    }

    /** @test */
    function users_can_create_new_key_pair()
    {
        $cf = new CertificateFactory();
        $cf->setType('ca')->setName(static::KEY_PAIR_NAME)->getKeyPair(true);

        $this->assertTrue(file_exists(WWW_ROOT . static::KEY_PAIR_NAME . DS . 'cert.crt'));
        $this->assertTrue(file_exists(WWW_ROOT . static::KEY_PAIR_NAME . DS . 'key.pem'));
    }

    /** @test */
    function users_can_encrypt_and_decrypt()
    {
        $string = json_encode([
            "name" => 'Hello',
            "surname" => 'world'
        ]);

        $this->sf->setString($string);
        $this->sf->setKeyPair(new KeyPairFileLoader(static::KEY_PAIR_NAME));
        $encrypted = base64_encode($this->sf->encrypt());

        $this->sf->setString(base64_decode($encrypted));
        $decrypted = $this->sf->decrypt();

        $this->assertEquals($string, $decrypted);
    }

    /** @test */
    function users_can_encrypt_and_decrypt_inverse()
    {
        $string = json_encode([
            "name" => 'Hello',
            "surname" => 'world'
        ]);

        $this->sf->setString($string);
        $this->sf->setKeyPair(new KeyPairFileLoader(static::KEY_PAIR_NAME));
        $encrypted = base64_encode($this->sf->publicEncrypt());

        $this->sf->setString(base64_decode($encrypted));
        $decrypted = $this->sf->privateDecrypt();

        $this->assertEquals($string, $decrypted);
    }
}