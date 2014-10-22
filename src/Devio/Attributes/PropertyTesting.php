<?php namespace Devio\Properties;

use Illuminate\Database\Eloquent\Model as Eloquent;

class PropertyTesting extends Eloquent {

    use PropertyTrait;

    public $table = 'properties_testing';

    protected $morphClass = 'PropertyTesting';

} 