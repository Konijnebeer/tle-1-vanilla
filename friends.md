# Friends and Messages Implementation

## Sending a Friend Request

- To send a friend request from user A to user B:
  - Ensure user1_id < user2_id to maintain order.
  - Insert into `friendships` table: `user1_id`, `user2_id`, `status = 'PENDING'`.
  - Handle acceptance/rejection by updating the status.

## Checking Friendship Before Sending a Message

- Before saving a message from sender to receiver:
  - Query `friendships` for a row where `(user1_id = sender AND user2_id = receiver) OR (user1_id = receiver AND user2_id = sender)` AND `status = 'ACCEPTED'`.
  - If a row exists, allow the message; else, deny.
