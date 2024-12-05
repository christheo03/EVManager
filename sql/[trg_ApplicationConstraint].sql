USE [vmarou01]
GO

/****** Object:  Trigger [vmarou01].[trg_ApplicationConstraint]    Script Date: 12/4/2024 4:31:06 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TRIGGER [vmarou01].[trg_ApplicationConstraint]
ON [vmarou01].[APPLICATION]
FOR INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Variables to hold values from the inserted table
    DECLARE @user_id INT, @subsidy_name NVARCHAR(255);

    -- Retrieve the values from the INSERTED pseudo-table
    SELECT 
        @user_id = user_id,
        @subsidy_name = subsidy_name
    FROM INSERTED;

   

    -- Check if the subsidy is in the C1-C13 group
    IF (@subsidy_name IN ('C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10', 'C11', 'C12', 'C13'))
    BEGIN
        IF EXISTS (
            SELECT 1
            FROM vmarou01.APPLICATION
            WHERE user_id = @user_id
              AND subsidy_name IN ('C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10', 'C11', 'C12', 'C13')
              AND app_id NOT IN (SELECT app_id FROM INSERTED) -- Exclude current insert
        )
        BEGIN
            RAISERROR ('A user can only make one application for subsidies C1-C13.', 16, 1);
            ROLLBACK;
            RETURN;
        END
    END
    ELSE IF (@subsidy_name = 'C14') -- Check if the subsidy is C14
    BEGIN
        IF EXISTS (
            SELECT 1
            FROM vmarou01.APPLICATION
            WHERE user_id = @user_id
              AND subsidy_name = 'C14'
              AND app_id NOT IN (SELECT app_id FROM INSERTED) -- Exclude current insert
        )
        BEGIN
            RAISERROR ('A user can only make one application for subsidy C14.', 16, 1);
            ROLLBACK;
            RETURN;
        END
    END

    -- Debugging print statement to confirm successful insert
END;
GO

ALTER TABLE [vmarou01].[APPLICATION] ENABLE TRIGGER [trg_ApplicationConstraint]
GO

