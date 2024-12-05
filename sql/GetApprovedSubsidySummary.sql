USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetApprovedSubsidySummary]    Script Date: 12/4/2024 4:16:01 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[GetApprovedSubsidySummary]
    @CategoryList NVARCHAR(MAX), -- Comma-separated list of subsidy names
    @OrderBy NVARCHAR(50) = 'TotalAmount', -- 'TotalAmount' or 'Category'
    @OrderDirection NVARCHAR(4) = 'DESC' -- 'ASC' or 'DESC'
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @SQL NVARCHAR(MAX);
    DECLARE @ParamDefinition NVARCHAR(MAX) = N'@CategoryList NVARCHAR(MAX)';

    -- Prepare the dynamic SQL for execution
    SET @SQL = N'SELECT 
                     S.name AS SubsidyName,
                     SUM(S.total_amount) AS TotalAmount
                 FROM vmarou01.APPLICATION A
                 JOIN vmarou01.SUBSIDY S ON A.subsidy_name = S.name
                 JOIN vmarou01.STATUS ST ON A.app_id = ST.app_id
                 WHERE ST.stage = ''Approved'' ';

    -- Filter by categories if provided
    IF LEN(@CategoryList) > 0
    BEGIN
        SET @SQL += N'AND S.name IN (SELECT value FROM STRING_SPLIT(@CategoryList, '','')) ';
    END

    SET @SQL += N'GROUP BY S.name ';

    -- Order by specified column and direction
    SET @SQL += CASE 
                    WHEN @OrderBy = 'Category' THEN N'ORDER BY S.name ' + @OrderDirection
                    ELSE N'ORDER BY TotalAmount ' + @OrderDirection
                END;

    -- Execute the dynamic SQL
    EXEC sp_executesql @SQL, @ParamDefinition, @CategoryList = @CategoryList;
END;
GO

