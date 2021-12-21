/**
 * External dependancies
 */
import Skeleton from 'react-loading-skeleton';

const PluginSettingsLoader = () => {
  return (
    <div>
      <Skeleton height={62} style={{ marginBottom: 16 }} />
      <Skeleton height={42} style={{ marginBottom: 16 }} />
      <Skeleton height={114} style={{ marginBottom: 1 }} />
    </div>
  );
};

export default PluginSettingsLoader;
