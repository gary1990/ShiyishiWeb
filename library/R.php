<?php

class R
{
	/**
	 * ����
	 *
	 * @var array
	 */
	static $_data = array();
	
	/**
	 * ע��һ��Key ��ֵ
	 *
	 * @param string $key
	 * @param mix $value
	 */
	public static function set($key, $value)
	{
		self::$_data[$key] = $value;
		return $value;
	}

	/**
	 * ���һ��Key��ֵ
	 *
	 * @param string $key
	 * @return mix
	 */
	public static function get($key)
	{
		if(!isset(self::$_data[$key]))
		{
			return null;
		}
		return self::$_data[$key];
	}
}
