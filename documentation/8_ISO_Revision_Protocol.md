# 8. ISO Document Revision Protocol

To maintain compliance with ISO quality standards and ensure documentation is updated every 5 years (or after major feature updates), follow this protocol.

## 1. Revision Workflow

1.  **Modify Source Files**:
    - All content is stored as individual `.md` files in the `documentation/` directory.
    - Edit the relevant file (e.g., `3_User_Manual.md`) using a text editor.
2.  **Versioning**:
    - Update the version number in `1_Introduction.md`.
    - Record the revision date and author in the **Revision History** (if implemented in the HTML).
3.  **Sync to Master HTML**:
    - Open `documentation/Full_Documentation.html`.
    - Manually synchronize the changes from the `.md` files into the corresponding HTML sections.
    - *Note: Ensure the CSS classes like `sop-list` or `code-block` are maintained for consistent styling.*

## 2. Screenshot & Asset Updates
1.  **Capture New UI**: If a feature's interface changes, take a high-resolution screenshot.
2.  **Storage**: Save screenshots in the `documentation/assets/` folder.
3.  **Linking**: Update the `<img>` tags in the HTML to point to the new asset.

## 3. Approval & Archiving
1.  **Review**: Perform a walk-through of the documentation with the Warehouse Manager.
2.  **PDF Export**:
    - Open `Full_Documentation.html` in Chrome/Edge.
    - Press `Ctrl + P`.
    - Select "Save as PDF".
    - Save as `System_Manual_V_X.X_YYYYMMDD.pdf`.
3.  **Archiving**:
    - Store the PDF in the company's shared drive or document management system.
    - Keep the previous version in an `archive/` folder for 5 years for audit traceability.

## 4. Revision Cycle
- **Major Revision**: Every 5 years (ISO Requirement).
- **Minor Revision**: After every feature update (e.g., V1.1, V1.2).
