# MOSALA+ Quick Reference Guide

## What's New?

### Admin Interface
**Admin Sidebar** - Completely redesigned with MOSALA+ colors
- Dashboard
- Management: Users, Services (CRUD), Jobs (CRUD)
- Interactions: **Job Applications** (NEW), **Service Requests** (NEW)
- Admin Settings
- Logout

### User Interface
**User Dashboard** - Enhanced with "Service Manquant?" button
- Quick modal form to request missing services
- Automatically notifies admins
- One-click navigation to all features

### New Views

#### Admin Job Applications
- **Route**: `GET /admin/job-applications`
- **Description**: Centralized view of all user applications
- **Features**:
  - Stats cards (Total, Pending, Approved)
  - Responsive table with applicant details
  - Quick status update buttons (Approve/Reject)
  - Real-time status changes via AJAX

#### Admin Service Requests
- **Route**: `GET /admin/service-requests`
- **Description**: Manage user-requested custom services
- **Features**:
  - Stats cards (Total, Pending, Addressed)
  - Card-based layout showing all request details
  - User contact information
  - Modal-based response form
  - Status tracking (Pending, Addressed, Resolved)
  - Admin response storage and display

### New User Features

#### Service Request Modal
- **Location**: User Dashboard (Right sidebar)
- **Button**: "Service Manquant?" (Congo Yellow)
- **Form Fields**:
  - Service name (required)
  - Category, City, Urgency
  - Budget range
  - Detailed description
  - Auto-populated contact info
- **Submission**: AJAX with loading states
- **Notification**: Admins receive notification

---

## Typical User Workflows

### Workflow 1: User Browses and Applies for Job
1. User clicks "Emplois" in sidebar
2. Sees list of admin-created jobs
3. Clicks job card to view details
4. Clicks "Postuler" button
5. Application saves to database
6. Admin sees it instantly in "Candidatures" section

### Workflow 2: User Requests Missing Service
1. User is on Dashboard
2. Clicks "Service Manquant?" button in Quick Actions
3. Modal opens with form
4. Fills in service details (name, category, budget, etc.)
5. Clicks "Envoyer ma Demande"
6. Form submits via AJAX
7. Success message appears
8. Admins get notified
9. Admin responds via modal in Service Requests view
10. User receives notification with admin's response

### Workflow 3: User Browses Services
1. User clicks "Services" in sidebar
2. Sees list of admin-created services
3. Can filter by category or search
4. Views service details (price, description, artisan)
5. Can contact artisan directly

---

## Typical Admin Workflows

### Workflow 1: Create Service
1. Admin navigates to "Gestion → Services"
2. Clicks "Create Service"
3. Fills in details (title, description, price, icon, category)
4. Saves
5. Service instantly appears in user "Services" view
6. Users can see and browse it immediately

### Workflow 2: Create Job Offer
1. Admin navigates to "Gestion → Offres d'Emploi"
2. Clicks "Create Job"
3. Fills in job details (title, description, salary, location, contract type)
4. Saves
5. Job instantly appears in user "Jobs" view
6. Users can apply immediately

### Workflow 3: Review and Respond to Service Requests
1. Admin navigates to "Interactions → Demandes Utilisateurs"
2. Sees card for each user request with:
   - Service name and user info
   - Budget range and location
   - Description
   - User's contact details
3. Clicks "Répondre" button
4. Modal opens with response form
5. Types response message
6. Selects status (Pending, Addressed, Resolved)
7. Submits
8. User receives notification with admin's response
9. Response displays on user's service request detail page

### Workflow 4: Manage Job Applications
1. Admin navigates to "Interactions → Candidatures"
2. Sees stats:
   - Total applications
   - Applications pending review
   - Applications approved
3. Views table of all applications
4. Clicks checkmark to approve or X to reject
5. Status updates immediately
6. User receives notification about status change

---

## Routes & Controllers Reference

### Admin Routes

```
GET  /admin/job-applications           → JobApplicationController@index
PATCH /admin/job-applications/{id}/status → JobApplicationController@updateStatus

GET  /admin/service-requests           → UserServiceRequestController@adminIndex
PATCH /admin/service-requests/{id}     → UserServiceRequestController@adminRespond
```

### User Routes

```
POST /user/service-requests            → UserServiceRequestController@store
GET  /user/service-requests            → UserServiceRequestController@index
GET  /user/service-requests/{id}       → UserServiceRequestController@show

GET  /user/services                    → UserServiceController@index
GET  /user/jobs                        → UserJobController@index
POST /user/jobs/{job}/apply            → UserJobController@apply
```

---

## Color System

### Primary Colors (MOSALA+)
- **Congo Blue** (#007FFF): Active states, primary buttons, icons
- **Congo Yellow** (#F7D000): Accents, alerts, "pending" status
- **Congo Red** (#CE1021): Logout button, delete actions, urgent items
- **MOSALA Light** (#F0F4F5): Background color for all pages

### Supporting Colors
- **White** (#FFFFFF): Card backgrounds
- **Gray 100** (#F3F4F6): Hover backgrounds
- **Gray 200** (#E5E7EB): Borders
- **Gray 400** (#9CA3AF): Placeholder text
- **Gray 500** (#6B7280): Secondary text
- **Gray 600** (#4B5563): Tertiary text
- **Gray 900** (#111827): Headings, primary text

---

## Key Components

### Admin Sidebar Button States
```
Inactive: Gray 600 text, Gray 400 icon, white hover
Active:   Congo Blue text, Congo Blue icon, white background + shadow
```

### Status Badges
```
Pending:   Yellow background, Yellow text
Addressed: Blue background, Blue text
Resolved:  Green background, Green text
Approved:  Green background, Green text
Rejected:  Red background, Red text
```

### Modal Form Structure
```
Header (sticky top): Icon + Title + Close button
Body: Form fields with labels and validation
Footer: Cancel + Submit buttons
```

---

## Common Issues & Solutions

### Issue: Service request form not submitting
**Check**:
1. Verify CSRF token is in form: `@csrf`
2. Check browser console for JavaScript errors
3. Verify route `user.service-requests.store` exists

### Issue: Admin doesn't see service request
**Check**:
1. Admin user must have role = 'admin' or 'super_admin'
2. Refresh page or navigate to service requests page
3. Check database: `ServiceRequest` record created

### Issue: User doesn't see job application status update
**Check**:
1. Admin clicked update button
2. Refresh page to see status change
3. Check if notification was sent

### Issue: Modal won't close
**Check**:
1. Click outside modal (overlay)
2. Press Escape key
3. Refresh page if stuck

---

## Performance Tips

1. **Database**: All queries use eager loading (`.with()`)
2. **Caching**: Route cache enabled, view cache enabled
3. **Frontend**: AJAX prevents full page reloads
4. **Images**: Use lightweight SVG icons
5. **Mobile**: Responsive design with mobile-first approach

---

## Future Enhancements

1. **Email Notifications**: Send emails for all events
2. **Real-time Updates**: WebSocket for instant notifications
3. **File Uploads**: Attach files to service requests
4. **Chat System**: Direct messaging between admin and user
5. **Analytics**: Detailed dashboard metrics
6. **Search**: Advanced filtering and full-text search
7. **Scheduling**: Calendar for job appointments
8. **Payments**: Integration with payment providers

---

## Support & Documentation

- **Full Implementation Guide**: `MOSALA_PLUS_SYSTEM_IMPLEMENTATION.md`
- **Design System**: `MOSALA_DESIGN_SYSTEM.md`
- **Authentication**: `AUTHENTICATION_GUIDE.md`
- **Database**: Check migrations in `database/migrations/`

---

**Version**: MOSALA+ v1.0
**Last Updated**: 2026-01-13
**Status**: ✅ Production Ready
