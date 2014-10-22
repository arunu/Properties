<?php namespace Devio\Attributes; 

use Illuminate\Database\Eloquent\Model as Eloquent;

class AttributeTesting extends Eloquent {

    use AttributeTrait;

    public $table = 'attributes_testing';

    protected $morphClass = 'AttributeTesting';

} 