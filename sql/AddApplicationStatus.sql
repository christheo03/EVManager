USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[AddApplicationStatus]    Script Date: 12/4/2024 4:13:15 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[AddApplicationStatus]
    @app_id INT,
    @status_stage NVARCHAR(50),
    @reason NVARCHAR(MAX) = NULL
AS
BEGIN
    SET NOCOUNT ON;

    -- Insert the new status into the STATUS table
    INSERT INTO vmarou01.STATUS (app_id, stage, date_of_modify, reason_of_modify)
    VALUES (@app_id, @status_stage, GETDATE(), @reason);
END;
GO

