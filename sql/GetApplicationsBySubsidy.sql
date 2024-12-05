USE [vmarou01]
GO

/****** Object:  StoredProcedure [dbo].[GetApplicationsBySubsidy]    Script Date: 12/4/2024 4:11:56 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[GetApplicationsBySubsidy]
    @SubsidyNames NVARCHAR(MAX) -- Comma-separated list of subsidy names
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @SQL NVARCHAR(MAX);
    DECLARE @ParamDefinition NVARCHAR(MAX) = N'@SubsidyNames NVARCHAR(MAX)';

    -- Prepare the dynamic SQL statement
    SET @SQL = N'SELECT
                     app_id,
                     date_of_app,
                     user_id,
                     subsidy_name,
                     carID
                 FROM vmarou01.APPLICATION
                 WHERE subsidy_name IN (SELECT value FROM STRING_SPLIT(@SubsidyNames, '',''))';

    -- Execute the dynamic SQL
    EXEC sp_executesql @SQL, @ParamDefinition, @SubsidyNames = @SubsidyNames;
END;
GO

