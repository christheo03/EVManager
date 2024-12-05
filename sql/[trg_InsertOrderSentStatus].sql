USE [vmarou01]
GO

/****** Object:  Trigger [vmarou01].[trg_InsertOrderSentStatus]    Script Date: 12/4/2024 4:32:04 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TRIGGER [vmarou01].[trg_InsertOrderSentStatus]
ON [vmarou01].[ORDER_VEHICLE]
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Insert a new status for the corresponding application
    INSERT INTO vmarou01.STATUS (app_id, stage, date_of_modify, reason_of_modify)
    SELECT 
        i.app_id,                             -- Application ID from inserted rows
        'order sent',                         -- New status
        GETDATE(),                            -- Current date as date_of_modify
        'Status automatically added when order is placed' -- Reason for the status
    FROM 
        inserted i;                           -- Referencing the newly inserted rows
END;
GO

ALTER TABLE [vmarou01].[ORDER_VEHICLE] ENABLE TRIGGER [trg_InsertOrderSentStatus]
GO

