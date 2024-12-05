USE [vmarou01]
GO

/****** Object:  StoredProcedure [dbo].[GetApplicationCountBySubsidyFiltered]    Script Date: 12/4/2024 4:11:16 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[GetApplicationCountBySubsidyFiltered]
    @SubsidyNames NVARCHAR(MAX), -- Comma-separated list of subsidy names
    @StartDate DATETIME = NULL,
    @EndDate DATETIME = NULL
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @SQL NVARCHAR(MAX);
    DECLARE @ParamDefinition NVARCHAR(MAX);

    -- Define the SQL query dynamically
    SET @SQL = N'SELECT 
                     subsidy_name,
                     COUNT(*) AS NumberOfApplications
                 FROM vmarou01.APPLICATION
                 WHERE (1=1) ';

    -- Filter by subsidy names if provided
    IF LEN(@SubsidyNames) > 0
    BEGIN
        SET @SQL += N' AND subsidy_name IN (SELECT value FROM STRING_SPLIT(@SubsidyNames, '',''))';
    END

    -- Filter by start date if provided
    IF @StartDate IS NOT NULL
    BEGIN
        SET @SQL += N' AND date_of_app >= @StartDate';
    END

    -- Filter by end date if provided
    IF @EndDate IS NOT NULL
    BEGIN
        SET @SQL += N' AND date_of_app <= @EndDate';
    END

    SET @SQL += N' GROUP BY subsidy_name
                  ORDER BY NumberOfApplications DESC;';

    -- Prepare the parameter definitions for the dynamic SQL
    SET @ParamDefinition = N'@SubsidyNames NVARCHAR(MAX),
                              @StartDate DATETIME,
                              @EndDate DATETIME';

    -- Execute the dynamic SQL
    EXEC sp_executesql @SQL, @ParamDefinition, 
        @SubsidyNames = @SubsidyNames, 
        @StartDate = @StartDate, 
        @EndDate = @EndDate;
END;
GO

