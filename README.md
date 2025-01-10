# Broadcast Messaging System

## Overview
A Laravel-based broadcast messaging system that allows administrators to send messages to users while efficiently tracking message states (read, unread, deleted) across multiple devices. The system is designed to handle message delivery and state management without creating unnecessary database records.

## Key Design Decisions

### Message State Management
- States are only created when a user first encounters a message
- Each user has only one state record per message, regardless of devices
- Uses soft deletes to maintain analytics data while allowing message removal
- Handles deleted/archived messages with clear visual indicators

### Database Structure
1. **broadcasts**
   - Core message content (title, content)
   - Uses soft deletes for maintaining analytics
   - Simple structure focusing on message content

2. **broadcast_user_states**
   - Tracks user interaction with messages
   - Created only when needed (lazy loading)
   - Unique constraint on broadcast_id and user_id
   - Includes read_at timestamp for engagement metrics
   - Uses soft deletes for state history

### User Experience Considerations
- Messages appear unread until viewed
- State persists across devices
- Clear visual indicators for message status:
  - New messages: Bold
  - Read messages: Normal
  - Archived messages: Orange
  - Deleted messages: Red
- Filterable inbox view with message counts

### Admin Features
- Create and manage broadcasts
- View detailed message metrics
- Track user engagement
- Manage message lifecycle

## Technical Implementation

### Models
- **Broadcast**: Handles message content and relationships
- **BroadcastUserState**: Manages user interaction states
- Both use soft deletes for data preservation

### Controllers
- Separated admin and user contexts
- Resource-based routing
- Efficient eager loading of relationships

### Views
- Uses Jetstream for consistent styling
- Alpine.js for dynamic filtering
- Responsive design
- Clean separation of admin and user interfaces

### Performance Considerations
- Lazy state creation
- Efficient queries with proper relationships
- Pagination for large datasets
- Minimal database records

## Future Considerations
- Message expiration handling
- Batch message operations
- Advanced analytics
- Message categories/targeting
- Rich content support

## Installation and Setup
1. Standard Laravel installation
2. Run migrations
3. Set up authentication (Jetstream)
4. Configure admin access

## Usage
### Admin
- Access /admin/broadcasts to manage messages
- Create new broadcasts
- View message metrics
- Manage message lifecycle

### Users
- Access /broadcasts/inbox to view messages
- Filter messages by status
- Read and interact with messages

## Security
- Admin middleware protection
- Soft deletes for data integrity
- Proper authorization checks
- Cross-device state management