<?php

namespace Gabievi\TBC;

use Illuminate\Support\Facades\Facade;

class TBCFacade extends Facade
{

	/**
	 * Get the registered name of the component.
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'tbc';
	}
}