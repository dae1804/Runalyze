<?php

namespace Runalyze\Model\Route;

/**
 * Generated by hand
 */
class ObjectTest extends \PHPUnit_Framework_TestCase {

	protected function simpleObject() {
		return new Object(array(
			Object::NAME => 'Test route',
			Object::CITIES => 'City A - City B',
			Object::DISTANCE => 3.14,
			Object::ELEVATION => 20,
			Object::ELEVATION_UP => 20,
			Object::ELEVATION_DOWN => 15,
			Object::GEOHASHES => array(47.7, 47.8),
			Object::ELEVATIONS_ORIGINAL => array(195, 210),
			Object::ELEVATIONS_CORRECTED => array(200, 220),
			Object::ELEVATIONS_SOURCE => 'unknown',
			Object::IN_ROUTENET => 1
		));
	}

	public function testEmptyObject() {
		$T = new Object();

		$this->assertFalse($T->hasPositionData());
		$this->assertFalse($T->has(Object::NAME));
		$this->assertFalse($T->has(Object::DISTANCE));
		$this->assertFalse($T->inRoutenet());
	}

	public function testSimpleObject() {
		$T = $this->simpleObject();

		$this->assertEquals('Test route', $T->name());
		$this->assertEquals(array('City A', 'City B'), $T->citiesAsArray());
		$this->assertEquals(3.14, $T->distance());
		$this->assertEquals(20, $T->elevation());
		$this->assertEquals(20, $T->elevationUp());
		$this->assertEquals(15, $T->elevationDown());
		$this->assertEquals(array(47.7, 47.8), $T->latitudes());
		$this->assertEquals(array(7.8, 7.7), $T->longitudes());
		$this->assertEquals(array(195, 210), $T->elevationsOriginal());
		$this->assertEquals(array(200, 220), $T->elevationsCorrected());
		$this->assertEquals('unknown', $T->get(Object::ELEVATIONS_SOURCE));
		$this->assertTrue($T->inRoutenet());
	}

	public function testSynchronization() {
		$T = $this->simpleObject();
		$T->synchronize();

		$this->assertEquals(47.7, $T->get(Object::STARTPOINT_LATITUDE));
		$this->assertEquals(7.8, $T->get(Object::STARTPOINT_LONGITUDE));
		$this->assertEquals(47.8, $T->get(Object::ENDPOINT_LATITUDE));
		$this->assertEquals(7.7, $T->get(Object::ENDPOINT_LONGITUDE));

		$this->assertEquals(47.7, $T->get(Object::MIN_LATITUDE));
		$this->assertEquals(7.7, $T->get(Object::MIN_LONGITUDE));
		$this->assertEquals(47.8, $T->get(Object::MAX_LATITUDE));
		$this->assertEquals(7.8, $T->get(Object::MAX_LONGITUDE));
	}

	public function testSynchronizationWithEmptyPoints() {
		$T = new Object(array(
			Object::LATITUDES => array(0.0, 47.7, 47.8, 0.0),
			Object::LONGITUDES => array(0.0, -7.8, -7.7, 0.0)
		));
		$T->synchronize();

		$this->assertEquals(47.7, $T->get(Object::STARTPOINT_LATITUDE));
		$this->assertEquals(-7.8, $T->get(Object::STARTPOINT_LONGITUDE));
		$this->assertEquals(47.8, $T->get(Object::ENDPOINT_LATITUDE));
		$this->assertEquals(-7.7, $T->get(Object::ENDPOINT_LONGITUDE));

		$this->assertEquals(47.7, $T->get(Object::MIN_LATITUDE));
		$this->assertEquals(-7.8, $T->get(Object::MIN_LONGITUDE));
		$this->assertEquals(47.8, $T->get(Object::MAX_LATITUDE));
		$this->assertEquals(-7.7, $T->get(Object::MAX_LONGITUDE));
	}

	public function testSynchronizationWithFullEmptyPoints() {
		$T = new Object(array(
			Object::LATITUDES => array(0.0, 0.0, 0.0, 0.0),
			Object::LONGITUDES => array(0.0, 0.0, 0.0, 0.0)
		));
		$T->synchronize();

		$this->assertEquals(null, $T->get(Object::STARTPOINT_LATITUDE));
		$this->assertEquals(null, $T->get(Object::STARTPOINT_LONGITUDE));
		$this->assertEquals(null, $T->get(Object::ENDPOINT_LATITUDE));
		$this->assertEquals(null, $T->get(Object::ENDPOINT_LONGITUDE));

		$this->assertEquals(null, $T->get(Object::MIN_LATITUDE));
		$this->assertEquals(null, $T->get(Object::MIN_LONGITUDE));
		$this->assertEquals(null, $T->get(Object::MAX_LATITUDE));
		$this->assertEquals(null, $T->get(Object::MAX_LONGITUDE));
	}

	/**
	 * @see https://github.com/Runalyze/Runalyze/issues/1172
	 */
	public function testPossibilityOfTooLargeCorrectedElevations() {
		$Object = new Object(array(
			Object::LATITUDES => array(49.440, 49.441, 49.442, 49.443, 49.444, 49.445, 49.446, 49.447, 49.448, 49.449, 49.450),
			Object::LONGITUDES => array(7.760, 7.761, 7.762, 7.763, 7.764, 7.765, 7.766, 7.767, 7.768, 7.769, 7.770),
			Object::ELEVATIONS_ORIGINAL => array(240, 238, 240, 238, 238, 237, 236, 237, 240, 248, 259),
			Object::ELEVATIONS_CORRECTED => array(240, 240, 240, 240, 240, 237, 237, 237, 237, 237, 259, 259, 259, 259, 259)
		));

		$this->assertEquals(11, $Object->num());
		$this->assertEquals(11, count($Object->elevationsCorrected()));
	}

	public function testSetGeohashes() {
		// - set geohashes
		// - check min/max
	}

	/**
	 * @todo
	 */
	public function testSetLatitudesLongitudes() {
		// - set latitudes/longitudes
		// - check min/max
		// - check some geohashes
	}

	/**
	 * @todo
	 */
	public function testThatSetLatitudesLongitudesMustHaveExpectedSize() {
		// - create object with elevations array
		// - set latitudes/longitudes with larger array
		// - exception should be thrown
	}

}
