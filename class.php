<?php

interface IAbstractValidator
{
    public function validate($value);
}

class IsNumeric implements IAbstractValidator
{
    public function validate($value)
    {
        return is_numeric($value);
    }
}

class IsEmail implements IAbstractValidator
{
    public function validate($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}

class IsRequired implements IAbstractValidator
{
    public function validate($value)
    {
        return ($value !== '' && $value !== NULL);
    }
}

class Validator implements IAbstractValidator
{
    protected $_validators;

    public function __construct($validator) {
        if(is_array($validator)) {
            foreach($validator as $v) {
                $this->addValidator($v);
            }
        } else {
            $this->addValidator($validator);
        }
    }

    public function addValidator(IAbstractValidator $validator)
    {
        $this->_validators[] = $validator;
        return $this;
    }

    public function validate($value)
    {

        foreach($this->_validators as $validator) {
            if ($validator->validate($value) === false) {
                return false;
            }
        }
        return true;
    }
}

abstract class AbstractForm {
    protected function rules() {
        return [];
    }


    public function submit() {
        if($this->validate()) {
            return "Form was submit";
        }

        return "Form is not valid";
    }

    public function validate() {
        foreach($this->rules() as $property => $rule) {
            if(property_exists($this, $property)){
                if(!$rule->validate($this->{$property}))
                    return false;
            }
        }

        return true;
    }
}

class ContactForm extends AbstractForm {
    public $email, $name;

    protected function rules() {
        return [
            'name' => new Validator(new IsRequired()),
            'email' => new Validator([new IsRequired(), new IsEmail()])
        ];
    }
}

$contactForm = new ContactForm();
$contactForm->name = "Nick";
$contactForm->email = "nick@test.ru";

echo $contactForm->submit();

$contactForm2 = new ContactForm();
$contactForm2->name = "Nick";
$contactForm2->email = "bad_email";

echo $contactForm2->submit();

