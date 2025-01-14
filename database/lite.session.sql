-- unreceived
SELECT
    broadcasts.id,
    broadcasts.title,
    broadcast_user_states.user_id
FROM
    broadcasts
    LEFT JOIN broadcast_user_states ON broadcast_user_states.broadcast_id = broadcasts.id
    AND broadcast_user_states.user_id = 2
WHERE
    broadcasts.deleted_at IS NULL
    AND broadcast_user_states.id IS NULL
ORDER BY
    broadcasts.id ASC;

-- unread but received
SELECT
    broadcasts.id,
    broadcasts.title,
    broadcast_user_states.user_id
FROM
    broadcasts
    INNER JOIN broadcast_user_states ON broadcast_user_states.broadcast_id = broadcasts.id
WHERE
    broadcasts.deleted_at IS NULL
    AND broadcast_user_states.user_id = 2
    AND broadcast_user_states.deleted_at IS NULL
    AND broadcast_user_states.read_at IS NULL
ORDER BY
    broadcasts.id ASC;

-- received and read
SELECT
    broadcasts.id,
    broadcasts.title,
    broadcast_user_states.user_id
FROM
    broadcasts
    INNER JOIN broadcast_user_states ON broadcast_user_states.broadcast_id = broadcasts.id
WHERE
    broadcasts.deleted_at IS NULL
    AND broadcast_user_states.user_id = 2
    AND broadcast_user_states.deleted_at IS NULL
    AND broadcast_user_states.read_at IS NOT NULL
ORDER BY
    broadcasts.id ASC;

-- received but deleted
SELECT
    broadcasts.id,
    broadcast_user_states.user_id
FROM
    broadcasts
    INNER JOIN broadcast_user_states ON broadcast_user_states.broadcast_id = broadcasts.id
WHERE
    broadcasts.deleted_at IS NULL
    AND broadcast_user_states.user_id = 2
    AND broadcast_user_states.deleted_at IS NOT NULL
ORDER BY
    broadcasts.id ASC;

-- archived
SELECT
    broadcasts.id,
    broadcasts.title,
    broadcast_user_states.user_id
FROM
    broadcasts
    LEFT JOIN broadcast_user_states ON broadcast_user_states.broadcast_id = broadcasts.id
    AND broadcast_user_states.user_id = 2
WHERE
    broadcasts.deleted_at IS NOT NULL
    AND (
        broadcast_user_states.id IS NULL
        OR broadcast_user_states.deleted_at IS NULL
    )
ORDER BY
    broadcasts.id ASC;