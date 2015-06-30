<?php
/**
 * This file contains class::Stroke
 * @package Runalyze\View\Activity\Plot\Series
 */

namespace Runalyze\View\Activity\Plot\Series;

use Runalyze\Model\Swim\Object as Swim;
use Runalyze\View\Activity;

/**
 * Plot for: Stroke
 * 
 * @author Hannes Christiansen & Michael Pohl
 * @package Runalyze\View\Activity\Plot\Series
 */
class Stroke extends ActivitySeries {
	/**
	 * @var string
	 */
	const COLOR = 'rgb(100,0,200)';

	/**
	 * Create series
	 * @var \Runalyze\View\Activity\Context $context
	 */
	public function __construct(Activity\Context $context) {
		$this->initOptions();
		$this->initData($context->swim(), Swim::STROKE);
	}

	/**
	 * Init options
	 */
	protected function initOptions() {
		$this->Label = __('Stroke');
		$this->Color = self::COLOR;

		$this->UnitString = '°C';
		$this->UnitDecimals = 1;

		$this->TickSize = 10;
		$this->TickDecimals = 0;

		$this->ShowAverage = true;
		$this->ShowMaximum = false;
		$this->ShowMinimum = false;
	}

	/**
	 * Average
	 * @param int $decimals [optional] 
	 * @return int
	 */
	protected function avg($decimals = 1) {
		return parent::avg($decimals);
	}
}