<?php
    
class Model_Proverb extends ORM
{
	public function get_random()
	{			
		return rand(0, 1) == 1
			? (object)Database::instance()->query(Database::SELECT,
			"SELECT 'sanonta' as type,
			(SELECT content
			FROM proverbs
			WHERE type = 'what'
			ORDER BY RAND()
			LIMIT 1) as `what`,
			(SELECT content
			FROM proverbs
			WHERE type = 'how'
			ORDER BY RAND()
			LIMIT 1) as `how`,
			(SELECT content
			FROM proverbs
			WHERE type = 'who'
			ORDER BY RAND()
			LIMIT 1) as `who`,
			(SELECT content
			FROM proverbs
			WHERE type = 'when'
			ORDER BY RAND()
			LIMIT 1) as `when`")->current()
			: (object)Database::instance()->query(Database::SELECT,
			"SELECT 'aforismi' as type,
			(SELECT content
			FROM proverbs
			WHERE type = 'what'
			ORDER BY RAND()
			LIMIT 1) as `what`,
			(SELECT content
			FROM proverbs
			WHERE type = 'author'
			ORDER BY RAND()
			LIMIT 1) as `who`")->current();
	}
}