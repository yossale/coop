<?
class Coop_OrderViewType
{
	const listType = "list";
	const galleryType = "gallery";

	private static $_session;
	
	private static function getSession()
	{
		if (!isset(self::$_session))
		{
			self::$_session = new Zend_Session_Namespace("listViewType");
		}
		return self::$_session;
	}
	
	public static function get()
	{
		if (!isset(self::getSession()->type))
		{
			self::getSession()->type = self::listType;
		}
		return self::getSession()->type;
	}
	
	public static function set($type)
	{
		if ($type != self::listType && $type != self::galleryType)
		{
			throw new Exception("invalid type: $type");
		}
		self::getSession()->type = $type;
	} 
}
