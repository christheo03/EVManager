USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[GetUserPassword]    Script Date: 12/4/2024 4:16:39 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[GetUserPassword]
    @username NVARCHAR(50)
AS
BEGIN
    -- Select the hashed password for the given username
    SELECT password
    FROM SIMPLE_USER
    WHERE username = @username;
END;
GO

