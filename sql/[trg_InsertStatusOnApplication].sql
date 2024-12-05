USE [vmarou01]
GO

/****** Object:  Trigger [vmarou01].[trg_InsertStatusOnApplication]    Script Date: 12/4/2024 4:31:18 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TRIGGER [vmarou01].[trg_InsertStatusOnApplication]
ON [vmarou01].[APPLICATION]
AFTER INSERT
AS
BEGIN
    -- Insert a new status into the STATUS table
    INSERT INTO STATUS (stage, date_of_modify, reason_of_modify, app_id)
    SELECT 
        'Active',                -- Set stage to 'Pending'
        GETDATE(),                -- Current date and time for date_of_modify
        NULL,                     -- No reason_of_modify initially
        app_id                    -- app_id from the newly inserted application
    FROM INSERTED;                -- 'INSERTED' table contains the newly added row(s)
END;
GO

ALTER TABLE [vmarou01].[APPLICATION] ENABLE TRIGGER [trg_InsertStatusOnApplication]
GO

