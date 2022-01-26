/**
 * External dependancies
 */
import Skeleton from 'react-loading-skeleton';

const ManageFontControlsLoader = () => {
  return (
    <div>
      <Skeleton height={62} style={{ marginBottom: 16 }} />
      <Skeleton height={51} style={{ marginBottom: 1 }} />
      <Skeleton height={72} style={{ marginBottom: 1 }} />
      <Skeleton height={72} style={{ marginBottom: 1 }} />
    </div>
  );
};

export default ManageFontControlsLoader;
