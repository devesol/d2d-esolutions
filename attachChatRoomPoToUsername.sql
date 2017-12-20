DROP PROCEDURE IF EXISTS attachChatroomPOToUserName;

DELIMITER |
CREATE PROCEDURE attachChatroomPOToUserName(
	IN arg_username VARCHAR(100), 
	IN arg_num_po VARCHAR(100) 
) 
BEGIN
	SELECT skypeid INTO @skypeid FROM kgift_jchat_userstatus
	WHERE userid = 	(
		SELECT id 
		FROM kgift_users
		WHERE username = arg_username
	)  
	;

	DELETE FROM kgift_user_usergroup_map 
	WHERE 1=1
	AND CONCAT(user_id, '||', group_id) IN (
		SELECT concat_user_group_id
		FROM (
			SELECT CONCAT(ku.id, '||', kg.id) as concat_user_group_id
			FROM kgift_users as ku
			LEFT JOIN kgift_user_usergroup_map as kug 
			ON kug.user_id = ku.id
			LEFT JOIN kgift_usergroups as kg
			ON kug.group_id = kg.id
			WHERE 1=1
			AND kg.title LIKE UPPER('PO_%')
			AND ku.username = arg_username
		) as main
	)	
	;
	
	INSERT INTO kgift_user_usergroup_map (group_id, user_id)
	SELECT
	(
		SELECT id 
		FROM kgift_usergroups 
		WHERE title = CONCAT('PO_', UPPER(arg_num_po))
	), 
	(
		SELECT id 
		FROM kgift_users
		WHERE username = arg_username
	)
	;

	DELETE FROM kgift_jchat_userstatus
	WHERE userid = 	(
		SELECT id 
		FROM kgift_users
		WHERE username = arg_username
	) 
	;

	INSERT INTO kgift_jchat_userstatus (userid, skypeid, roomid)
	SELECT
	(
		SELECT id 
		FROM kgift_users
		WHERE username = arg_username
	), 
	@skypeid,
	(
		SELECT id 
		FROM kgift_jchat_rooms 
		WHERE name = UPPER(arg_num_po)
	)
	;

	SELECT @skypeid;
END|