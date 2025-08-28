Project description: Web App Developer Needed to Replace Complex Spreadsheet for Online Learning Center

I run an online learning center, and I currently rely on a large, complex spreadsheet to manage all of our class data and financial calculations. I would like to have a custom web app tool built to replace this spreadsheet and make the process more efficient, scalable, and user-friendly.

The tool will need to:
- Track both 1:1 and group classes.
- For group classes, calculate teacher pay as a base pay + a variable bonus that depends on the number of learners enrolled (which changes weekly).
- For 1:1 classes, keep track of ongoing weekly enrollments, including when students pause or cancel their subscription.
- Handle reschedules, cancellations, and substitute teachers, ensuring payouts and class records remain accurate.
- Provide automated revenue, profit, and teacher payout calculations based on class and enrollment data.
- Ability to export/download reports (CSV, Excel, or PDF).

User Roles (3 Login Levels): 
- Owner (me): Full access to all data, including financials. 
- Admin: Access to all class and scheduling data, but not financial data. 
- Teachers: Access to only their own classes and payouts. 

The system should NOT cover payments, only the INFORMATION about prices, calculations and payouts.

---

## Plan of Action

### Phase 1: Core Entities

1. User resource (teachers/admins)
2. Student resource (independent entity)
3. LearningClass resource (core business entity)

### Phase 2: Operations

4. Enrollment resource (student subscriptions, depends on Student + LearningClass)
5. ClassSchedule resource (scheduling + substitutes, depends on LearningClass + User)

### Phase 3: Advanced

6. Attendance resource (tracking, depends on Student + ClassSchedule)
7. TeacherPayout resource (financial calculations, depends on User + ClassSchedule)
