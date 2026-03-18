# 1. Introduction - Tools Asset Management System V1.1

## Project Overview
**Tools Asset Management System Genting Energy Group** is a bespoke, enterprise-level digital platform designed to provide total visibility and strict governance over the industrial and laboratory assets of the Genting Energy Group.

This system has been upgraded to **Version 1.1** to include more robust data integrity features and seamless personnel synchronization.

## Strategic Pillars
- **Hierarchical Storage Precision**: Tracking assets down to specific Buildings, Rooms, Racks, and Bins.
- **LDAP & Biometric Integration**: Integrated with corporate LDAP for seamless employee data synchronization.
- **PO-Based Tracking**: Every tool is now linked to a Purchase Order (PO Number) for procurement transparency.
- **Data Integrity & Traceability**: Implementation of Soft Deletes and `deleted_by` logging for audit-ready records.

## Global System Scope (13 Core Modules)

### 1. The Foundation (Master Data Management)
The system establishes a robust data foundation before any activities occur:
- **Spatial Governance**: Managing Locations, Racks, and Bins.
- **Asset Taxonomy**: Differentiating between Asset Types, Material Groups, and Sub-Groups.
- **Supply Network**: Comprehensive registries for Manufacturers and Calibration Vendors.
- **Personnel Hub**: Detailed records for every employee authorized to borrow assets.
- **Asset Master**: The central integration point where physical tools are mapped to technical and storage data.

### 2. The Operational Engine (Transactions)
Every physical movement is recorded in the digital log:
- **Lending Control**: Electronic loan issuance and return tracking for operational tools.
- **Incident Governance**: Official reporting flows for Damaged or Lost assets.
- **Service Management**: Formal calibration issuing and receiving workflows.
- **Internal Migration**: Accountability during asset movement between warehouses (Mutation).

### 3. Business Intelligence (Analytics)
- **Live Monitoring Dashboard**: Real-time asset availability and health summaries.
- **Automated Communication**: Scheduler-driven email alerts for overdue items and maintenance windows.
- **Historical Audit Center**: Exhaustive transactional logging for compliance and procurement review.

## Document Purpose
This documentation serves as the official operational manual for all Genting Energy Group warehouse and facility staff, providing exhaustive step-by-step instructions for system usage and detailed resolution procedures for operational hurdles.
