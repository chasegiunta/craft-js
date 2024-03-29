export type Address = {
  ownerId: number | null;
  countryCode: string;
  administrativeArea: string | null;
  locality: string | null;
  dependentLocality: string | null;
  postalCode: string | null;
  sortingCode: string | null;
  addressLine1: string | null;
  addressLine2: string | null;
  organization: string | null;
  organizationTaxId: string | null;
  latitude: string | null;
  longitude: string | null;
  id: number | null;
  tempId: string | null;
  draftId: number | null;
  revisionId: number | null;
  isProvisionalDraft: boolean;
  uid: string | null;
  siteSettingsId: number | null;
  fieldLayoutId: number | null;
  structureId: number | null;
  contentId: number | null;
  enabled: boolean;
  archived: boolean;
  siteId: number | null;
  title: string | null;
  slug: string | null;
  uri: string | null;
  dateCreated: any | null;
  dateUpdated: any | null;
  dateLastMerged: any | null;
  dateDeleted: any | null;
  root: number | null;
  lft: number | null;
  rgt: number | null;
  level: number | null;
  searchScore: number | null;
  trashed: boolean;
  awaitingFieldValues: boolean;
  propagating: boolean;
  validatingRelatedElement: boolean;
  propagateAll: boolean;
  newSiteIds: Array<number>;
  isNewForSite: boolean;
  resaving: boolean;
  duplicateOf: any | null;
  firstSave: boolean;
  mergingCanonicalChanges: boolean;
  updatingFromDerivative: boolean;
  previewing: boolean;
  hardDelete: boolean;
  fullName: string | null;
  firstName: string | null;
  lastName: string | null;
};
export type AddressQuery = {
  ownerId: any;
  countryCode: any;
  administrativeArea: any;
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
export type Asset = {
  isFolder: boolean;
  sourcePath: Array<any> | null;
  folderId: number | null;
  uploaderId: number | null;
  folderPath: string | null;
  kind: string | null;
  alt: string | null;
  size: number | null;
  keptFile: boolean | null;
  dateModified: any | null;
  newLocation: string | null;
  locationError: string | null;
  newFilename: string | null;
  newFolderId: number | null;
  tempFilePath: string | null;
  avoidFilenameConflicts: boolean;
  suggestedFilename: string | null;
  conflictingFilename: string | null;
  deletedWithVolume: boolean;
  keepFileOnDelete: boolean;
  id: number | null;
  tempId: string | null;
  draftId: number | null;
  revisionId: number | null;
  isProvisionalDraft: boolean;
  uid: string | null;
  siteSettingsId: number | null;
  fieldLayoutId: number | null;
  structureId: number | null;
  contentId: number | null;
  enabled: boolean;
  archived: boolean;
  siteId: number | null;
  title: string | null;
  slug: string | null;
  uri: string | null;
  dateCreated: any | null;
  dateUpdated: any | null;
  dateLastMerged: any | null;
  dateDeleted: any | null;
  root: number | null;
  lft: number | null;
  rgt: number | null;
  level: number | null;
  searchScore: number | null;
  trashed: boolean;
  awaitingFieldValues: boolean;
  propagating: boolean;
  validatingRelatedElement: boolean;
  propagateAll: boolean;
  newSiteIds: Array<number>;
  isNewForSite: boolean;
  resaving: boolean;
  duplicateOf: any | null;
  firstSave: boolean;
  mergingCanonicalChanges: boolean;
  updatingFromDerivative: boolean;
  previewing: boolean;
  hardDelete: boolean;
};
export type AssetQuery = {
  editable: boolean | null;
  savable: boolean | null;
  volumeId: any;
  folderId: any;
  uploaderId: number | null;
  filename: any;
  kind: any;
  hasAlt: boolean | null;
  width: any;
  height: any;
  size: any;
  dateModified: any;
  includeSubfolders: boolean;
  folderPath: string | null;
  withTransforms: any;
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
export type Category = {
  groupId: number | null;
  deletedWithGroup: boolean;
  id: number | null;
  tempId: string | null;
  draftId: number | null;
  revisionId: number | null;
  isProvisionalDraft: boolean;
  uid: string | null;
  siteSettingsId: number | null;
  fieldLayoutId: number | null;
  structureId: number | null;
  contentId: number | null;
  enabled: boolean;
  archived: boolean;
  siteId: number | null;
  title: string | null;
  slug: string | null;
  uri: string | null;
  dateCreated: any | null;
  dateUpdated: any | null;
  dateLastMerged: any | null;
  dateDeleted: any | null;
  root: number | null;
  lft: number | null;
  rgt: number | null;
  level: number | null;
  searchScore: number | null;
  trashed: boolean;
  awaitingFieldValues: boolean;
  propagating: boolean;
  validatingRelatedElement: boolean;
  propagateAll: boolean;
  newSiteIds: Array<number>;
  isNewForSite: boolean;
  resaving: boolean;
  duplicateOf: any | null;
  firstSave: boolean;
  mergingCanonicalChanges: boolean;
  updatingFromDerivative: boolean;
  previewing: boolean;
  hardDelete: boolean;
};
export type CategoryQuery = {
  editable: boolean;
  groupId: any;
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
export type EagerLoadPlan = {
  handle: string | null;
  alias: string | null;
  criteria: Array<any>;
  all: boolean;
  count: boolean;
  when: null;
  nested: Array<EagerLoadPlan>;
};
export type ElementCollection = {};
export type ElementQuery = {
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
export type ElementQueryInterface = {};
export type ElementRelationParamParser = {
  fields: Array<any> | null;
};
export type Entry = {
  sectionId: number | null;
  postDate: any | null;
  expiryDate: any | null;
  deletedWithEntryType: boolean;
  _authorId: number | null;
  id: number | null;
  tempId: string | null;
  draftId: number | null;
  revisionId: number | null;
  isProvisionalDraft: boolean;
  uid: string | null;
  siteSettingsId: number | null;
  fieldLayoutId: number | null;
  structureId: number | null;
  contentId: number | null;
  enabled: boolean;
  archived: boolean;
  siteId: number | null;
  title: string | null;
  slug: string | null;
  uri: string | null;
  dateCreated: any | null;
  dateUpdated: any | null;
  dateLastMerged: any | null;
  dateDeleted: any | null;
  root: number | null;
  lft: number | null;
  rgt: number | null;
  level: number | null;
  searchScore: number | null;
  trashed: boolean;
  awaitingFieldValues: boolean;
  propagating: boolean;
  validatingRelatedElement: boolean;
  propagateAll: boolean;
  newSiteIds: Array<number>;
  isNewForSite: boolean;
  resaving: boolean;
  duplicateOf: any | null;
  firstSave: boolean;
  mergingCanonicalChanges: boolean;
  updatingFromDerivative: boolean;
  previewing: boolean;
  hardDelete: boolean;
};
export type EntryQuery = {
  editable: boolean | null;
  savable: boolean | null;
  sectionId: any;
  typeId: any;
  authorId: any;
  authorGroupId: any;
  postDate: any;
  before: any;
  after: any;
  expiryDate: any;
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
export type GlobalSet = {
  name: string | null;
  handle: string | null;
  sortOrder: number | null;
  id: number | null;
  tempId: string | null;
  draftId: number | null;
  revisionId: number | null;
  isProvisionalDraft: boolean;
  uid: string | null;
  siteSettingsId: number | null;
  fieldLayoutId: number | null;
  structureId: number | null;
  contentId: number | null;
  enabled: boolean;
  archived: boolean;
  siteId: number | null;
  title: string | null;
  slug: string | null;
  uri: string | null;
  dateCreated: any | null;
  dateUpdated: any | null;
  dateLastMerged: any | null;
  dateDeleted: any | null;
  root: number | null;
  lft: number | null;
  rgt: number | null;
  level: number | null;
  searchScore: number | null;
  trashed: boolean;
  awaitingFieldValues: boolean;
  propagating: boolean;
  validatingRelatedElement: boolean;
  propagateAll: boolean;
  newSiteIds: Array<number>;
  isNewForSite: boolean;
  resaving: boolean;
  duplicateOf: any | null;
  firstSave: boolean;
  mergingCanonicalChanges: boolean;
  updatingFromDerivative: boolean;
  previewing: boolean;
  hardDelete: boolean;
};
export type GlobalSetQuery = {
  editable: boolean;
  handle: string | Array<string> | null;
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
export type MatrixBlock = {
  fieldId: number | null;
  primaryOwnerId: number | null;
  ownerId: number | null;
  typeId: number | null;
  sortOrder: number | null;
  dirty: boolean;
  collapsed: boolean;
  deletedWithOwner: boolean;
  saveOwnership: boolean;
  id: number | null;
  tempId: string | null;
  draftId: number | null;
  revisionId: number | null;
  isProvisionalDraft: boolean;
  uid: string | null;
  siteSettingsId: number | null;
  fieldLayoutId: number | null;
  structureId: number | null;
  contentId: number | null;
  enabled: boolean;
  archived: boolean;
  siteId: number | null;
  title: string | null;
  slug: string | null;
  uri: string | null;
  dateCreated: any | null;
  dateUpdated: any | null;
  dateLastMerged: any | null;
  dateDeleted: any | null;
  root: number | null;
  lft: number | null;
  rgt: number | null;
  level: number | null;
  searchScore: number | null;
  trashed: boolean;
  awaitingFieldValues: boolean;
  propagating: boolean;
  validatingRelatedElement: boolean;
  propagateAll: boolean;
  newSiteIds: Array<number>;
  isNewForSite: boolean;
  resaving: boolean;
  duplicateOf: any | null;
  firstSave: boolean;
  mergingCanonicalChanges: boolean;
  updatingFromDerivative: boolean;
  previewing: boolean;
  hardDelete: boolean;
};
export type MatrixBlockQuery = {
  fieldId: any;
  primaryOwnerId: any;
  ownerId: any;
  allowOwnerDrafts: boolean | null;
  allowOwnerRevisions: boolean | null;
  typeId: any;
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
export type Tag = {
  groupId: number | null;
  deletedWithGroup: boolean;
  id: number | null;
  tempId: string | null;
  draftId: number | null;
  revisionId: number | null;
  isProvisionalDraft: boolean;
  uid: string | null;
  siteSettingsId: number | null;
  fieldLayoutId: number | null;
  structureId: number | null;
  contentId: number | null;
  enabled: boolean;
  archived: boolean;
  siteId: number | null;
  title: string | null;
  slug: string | null;
  uri: string | null;
  dateCreated: any | null;
  dateUpdated: any | null;
  dateLastMerged: any | null;
  dateDeleted: any | null;
  root: number | null;
  lft: number | null;
  rgt: number | null;
  level: number | null;
  searchScore: number | null;
  trashed: boolean;
  awaitingFieldValues: boolean;
  propagating: boolean;
  validatingRelatedElement: boolean;
  propagateAll: boolean;
  newSiteIds: Array<number>;
  isNewForSite: boolean;
  resaving: boolean;
  duplicateOf: any | null;
  firstSave: boolean;
  mergingCanonicalChanges: boolean;
  updatingFromDerivative: boolean;
  previewing: boolean;
  hardDelete: boolean;
};
export type TagQuery = {
  groupId: any;
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
export type User = {
  photoId: number | null;
  active: boolean;
  pending: boolean;
  locked: boolean;
  suspended: boolean;
  admin: boolean;
  username: string | null;
  email: string | null;
  password: string | null;
  lastLoginDate: any | null;
  invalidLoginCount: number | null;
  lastInvalidLoginDate: any | null;
  lockoutDate: any | null;
  hasDashboard: boolean;
  passwordResetRequired: boolean;
  lastPasswordChangeDate: any | null;
  unverifiedEmail: string | null;
  newPassword: string | null;
  currentPassword: string | null;
  verificationCodeIssuedDate: any | null;
  verificationCode: string | null;
  lastLoginAttemptIp: string | null;
  authError: string | null;
  inheritorOnDelete: User | null;
  id: number | null;
  tempId: string | null;
  draftId: number | null;
  revisionId: number | null;
  isProvisionalDraft: boolean;
  uid: string | null;
  siteSettingsId: number | null;
  fieldLayoutId: number | null;
  structureId: number | null;
  contentId: number | null;
  enabled: boolean;
  archived: boolean;
  siteId: number | null;
  title: string | null;
  slug: string | null;
  uri: string | null;
  dateCreated: any | null;
  dateUpdated: any | null;
  dateLastMerged: any | null;
  dateDeleted: any | null;
  root: number | null;
  lft: number | null;
  rgt: number | null;
  level: number | null;
  searchScore: number | null;
  trashed: boolean;
  awaitingFieldValues: boolean;
  propagating: boolean;
  validatingRelatedElement: boolean;
  propagateAll: boolean;
  newSiteIds: Array<number>;
  isNewForSite: boolean;
  resaving: boolean;
  duplicateOf: any | null;
  firstSave: boolean;
  mergingCanonicalChanges: boolean;
  updatingFromDerivative: boolean;
  previewing: boolean;
  hardDelete: boolean;
  fullName: string | null;
  firstName: string | null;
  lastName: string | null;
};
export type UserQuery = {
  admin: boolean | null;
  authors: boolean | null;
  assetUploaders: boolean | null;
  hasPhoto: boolean | null;
  can: any;
  groupId: any;
  email: any;
  username: any;
  fullName: any;
  firstName: any;
  lastName: any;
  lastLoginDate: any;
  withGroups: boolean;
  elementType: string;
  query: any | null;
  subQuery: any | null;
  contentTable: string | null;
  customFields: Array<any> | null;
  inReverse: boolean;
  asArray: boolean;
  ignorePlaceholders: boolean;
  drafts: boolean | null;
  provisionalDrafts: boolean | null;
  draftId: number | null;
  draftOf: any;
  draftCreator: number | null;
  savedDraftsOnly: boolean;
  revisions: boolean | null;
  revisionId: number | null;
  revisionOf: number | null;
  revisionCreator: number | null;
  id: any;
  uid: any;
  siteSettingsId: any;
  fixedOrder: boolean;
  status: string | Array<string> | null;
  archived: boolean;
  trashed: boolean | null;
  dateCreated: any;
  dateUpdated: any;
  siteId: any;
  unique: boolean;
  preferSites: Array<any> | null;
  leaves: boolean;
  relatedTo: any;
  title: any;
  slug: any;
  uri: any;
  search: any;
  ref: any;
  with: string | Array<any> | null;
  orderBy: any;
  withStructure: boolean | null;
  structureId: any;
  level: any;
  hasDescendants: boolean | null;
  ancestorOf: number | any | null;
  ancestorDist: number | null;
  descendantOf: number | any | null;
  descendantDist: number | null;
  siblingOf: number | any | null;
  prevSiblingOf: number | any | null;
  nextSiblingOf: number | any | null;
  positionedBefore: number | any | null;
  positionedAfter: number | any | null;
  select: Array<any> | null;
  selectOption: string | null;
  distinct: boolean;
  from: Array<any> | null;
  groupBy: Array<any> | null;
  join: Array<any> | null;
  having: string | Array<any> | any | null;
  union: Array<any> | null;
  withQueries: Array<any> | null;
  params: Array<any> | null;
  queryCacheDuration: number | boolean | null;
  queryCacheDependency: any | null;
  where: string | Array<any> | any | null;
  limit: number | any | null;
  offset: number | any | null;
  indexBy: string | null;
  emulateExecution: boolean;
};
