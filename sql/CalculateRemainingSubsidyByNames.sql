USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[CalculateRemainingSubsidyByNames]    Script Date: 12/4/2024 4:13:35 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [vmarou01].[CalculateRemainingSubsidyByNames]
    @SubsidyNames NVARCHAR(MAX) = NULL, -- Making the parameter optional by defaulting to NULL
    @StartDate DATE = NULL, -- Optional start date
    @EndDate DATE = NULL, -- Optional end date
    @OrderDirection NVARCHAR(4) = 'ASC' -- Default ordering to ascending; can be 'ASC' or 'DESC'
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @SQL NVARCHAR(MAX);
    DECLARE @ParamDefinition NVARCHAR(MAX) = N'@SubsidyNames NVARCHAR(MAX), @StartDate DATE, @EndDate DATE';

    -- Prepare the dynamic SQL statement to calculate remaining subsidy amounts
    SET @SQL = N'SELECT
                     A.subsidy_name,
                     S.total_amount - ISNULL(SUM(S.amount), 0) AS RemainingAmount
                 FROM vmarou01.APPLICATION A
                 INNER JOIN vmarou01.SUBSIDY S ON A.subsidy_name = S.name
                 INNER JOIN vmarou01.STATUS ST ON A.app_id = ST.app_id
                 WHERE ST.stage = ''Approved'' ';

    -- Include only specified subsidy names if provided
    IF LEN(ISNULL(@SubsidyNames, '')) > 0
    BEGIN
        SET @SQL += N' AND A.subsidy_name IN (SELECT value FROM STRING_SPLIT(@SubsidyNames, '',''))';
    END

    -- Filter by start date if provided
    IF @StartDate IS NOT NULL
    BEGIN
        SET @SQL += N' AND A.date_of_app >= @StartDate';  -- Corrected column name
    END

    -- Filter by end date if provided
    IF @EndDate IS NOT NULL
    BEGIN
        SET @SQL += N' AND A.date_of_app <= @EndDate';  -- Corrected column name
    END

    SET @SQL += N' GROUP BY A.subsidy_name, S.total_amount';
    SET @SQL += N' ORDER BY A.subsidy_name ' + @OrderDirection;

    -- Execute the dynamic SQL
    EXEC sp_executesql @SQL, @ParamDefinition, 
        @SubsidyNames = @SubsidyNames, 
        @StartDate = @StartDate, 
        @EndDate = @EndDate;
END;
GO

