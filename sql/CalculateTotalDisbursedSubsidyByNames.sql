USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[CalculateTotalDisbursedSubsidyByNames]    Script Date: 12/4/2024 4:14:01 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [vmarou01].[CalculateTotalDisbursedSubsidyByNames]
    @SubsidyNames NVARCHAR(MAX), -- Comma-separated list of subsidy names
    @StartDate DATE = NULL, -- Optional start date
    @EndDate DATE = NULL,   -- Optional end date
    @OrderDirection NVARCHAR(4) = 'ASC' -- 'ASC' or 'DESC', default is 'ASC'
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @SQL NVARCHAR(MAX);
    DECLARE @ParamDefinition NVARCHAR(MAX) = N'@SubsidyNames NVARCHAR(MAX), @StartDate DATE, @EndDate DATE, @OrderDirection NVARCHAR(4)';

    -- Prepare the dynamic SQL statement
    SET @SQL = N'SELECT
                     A.subsidy_name,
                     COUNT(A.app_id) AS NumberOfApprovedApplications,
                     S.amount AS AmountPerApplication,
                     COUNT(A.app_id) * S.amount AS TotalDisbursedAmount
                 FROM vmarou01.APPLICATION A
                 INNER JOIN vmarou01.SUBSIDY S ON A.subsidy_name = S.name
                 INNER JOIN vmarou01.STATUS ST ON A.app_id = ST.app_id
                 WHERE ST.stage = ''Approved'' ';

    -- Filter by subsidy names if provided
    IF LEN(@SubsidyNames) > 0
    BEGIN
        SET @SQL += N' AND A.subsidy_name IN (SELECT value FROM STRING_SPLIT(@SubsidyNames, '',''))';
    END

    -- Apply date filters if provided
    IF @StartDate IS NOT NULL
    BEGIN
        SET @SQL += N' AND A.date_of_app >= @StartDate';
    END
    IF @EndDate IS NOT NULL
    BEGIN
        SET @SQL += N' AND A.date_of_app <= @EndDate';
    END

    -- Group and order the results as specified
    SET @SQL += N' GROUP BY A.subsidy_name, S.amount';
    SET @SQL += N' ORDER BY A.subsidy_name ' + @OrderDirection;

    -- Execute the dynamic SQL
    EXEC sp_executesql @SQL, @ParamDefinition, 
        @SubsidyNames = @SubsidyNames, 
        @StartDate = @StartDate, 
        @EndDate = @EndDate, 
        @OrderDirection = @OrderDirection;
END;
GO

