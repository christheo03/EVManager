USE [vmarou01]
GO

/****** Object:  StoredProcedure [vmarou01].[InsertDocument]    Script Date: 12/4/2024 4:17:24 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [vmarou01].[InsertDocument]
    @type NVARCHAR(100),
    @size_of_doc BIGINT,
    @path NVARCHAR(MAX),
    @app_id INT,
    @title NVARCHAR(255),
	@category NVARCHAR(50),
    @doc_id INT OUTPUT -- Output parameter to retrieve the generated document ID
AS
BEGIN
    SET NOCOUNT ON;

    -- Insert a new document record, automatically setting the 'up_date' to the current timestamp
    INSERT INTO DOCUMENT (type, size_of_doc, path, app_id, title, up_date,category)
    VALUES (@type, @size_of_doc, @path, @app_id, @title, GETDATE(),@category);

    -- Return the newly generated doc_id
    SET @doc_id = SCOPE_IDENTITY();
END;
GO

