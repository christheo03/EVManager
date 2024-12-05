USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[CheckApplicationExistence]    Script Date: 12/4/2024 4:14:16 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[CheckApplicationExistence]
    @app_id INT,
    @user_id INT,
    @subsidy_name NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    -- Check if the application exists
    SELECT CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM APPLICATION 
            WHERE app_id = @app_id AND user_id = @user_id AND subsidy_name = @subsidy_name
        ) THEN 1 
        ELSE 0 
    END AS ApplicationExists;
END;
GO

