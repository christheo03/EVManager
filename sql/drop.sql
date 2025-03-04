USE [vmarou01]
GO
ALTER TABLE [vmarou01].[STATUS] DROP CONSTRAINT [FK_STATUS_APPLICATION]
GO
ALTER TABLE [vmarou01].[STATUS] DROP CONSTRAINT [FK__STATUS__app_id__58D1301D]
GO
ALTER TABLE [vmarou01].[ORDER_VEHICLE] DROP CONSTRAINT [FK_OrderVehicle_AAID]
GO
ALTER TABLE [vmarou01].[ORDER_VEHICLE] DROP CONSTRAINT [FK_ORDER_APPLICATION]
GO
ALTER TABLE [vmarou01].[DOCUMENT] DROP CONSTRAINT [FK_DOCUMENT_APPLICATION]
GO
ALTER TABLE [vmarou01].[DOCUMENT] DROP CONSTRAINT [FK__DOCUMENT__app_id__5CA1C101]
GO
ALTER TABLE [vmarou01].[APPLICATION] DROP CONSTRAINT [FK_APPLICATION_USER]
GO
ALTER TABLE [vmarou01].[APPLICATION] DROP CONSTRAINT [FK_APPLICATION_SUBSIDY_NAME]
GO
/****** Object:  Table [vmarou01].[SUBSIDY]    Script Date: 12/4/2024 4:26:11 AM ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[vmarou01].[SUBSIDY]') AND type in (N'U'))
DROP TABLE [vmarou01].[SUBSIDY]
GO
/****** Object:  Table [vmarou01].[STATUS]    Script Date: 12/4/2024 4:26:12 AM ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[vmarou01].[STATUS]') AND type in (N'U'))
DROP TABLE [vmarou01].[STATUS]
GO
/****** Object:  Table [vmarou01].[SIMPLE_USER]    Script Date: 12/4/2024 4:26:12 AM ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[vmarou01].[SIMPLE_USER]') AND type in (N'U'))
DROP TABLE [vmarou01].[SIMPLE_USER]
GO
/****** Object:  Table [vmarou01].[ORDER_VEHICLE]    Script Date: 12/4/2024 4:26:12 AM ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[vmarou01].[ORDER_VEHICLE]') AND type in (N'U'))
DROP TABLE [vmarou01].[ORDER_VEHICLE]
GO
/****** Object:  Table [vmarou01].[DOCUMENT]    Script Date: 12/4/2024 4:26:12 AM ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[vmarou01].[DOCUMENT]') AND type in (N'U'))
DROP TABLE [vmarou01].[DOCUMENT]
GO
/****** Object:  Table [vmarou01].[APPLICATION]    Script Date: 12/4/2024 4:26:12 AM ******/
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[vmarou01].[APPLICATION]') AND type in (N'U'))
DROP TABLE [vmarou01].[APPLICATION]
GO
